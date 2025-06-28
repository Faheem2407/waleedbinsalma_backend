<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineStore;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class ProductPurchaseController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function productPurchase(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'store_id' => 'required|numeric|exists:online_stores,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|numeric',
            'address_id' => 'required|numeric|exists:addresses,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|numeric|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'success_redirect_url' => 'required|url',
            'cancel_redirect_url' => 'required|url',
        ]);

        if ($validateData->fails()) {
            return $this->error($validateData->errors(), $validateData->errors()->first(), 422);
        }

        $user = auth()->user();
        if (!$user) {
            return $this->error([], 'User not found.', 200);
        }

        try {
            $onlineStore = OnlineStore::find($request->store_id);
            if (!$onlineStore) {
                return $this->error([], 'Online store not found.', 200);
            }

            $destination = $onlineStore->businessProfile->bankDetail->stripe_account_id ?? null;
            if (!$destination) {
                return $this->error([], 'Shop owner Stripe account not connected.', 200);
            }

            $productInputs = collect($request->products);
            $productIds = $productInputs->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->get();

            $percentageToTake = 10; // platform fee in %
            $lineItems = [];
            $totalAmount = 0;
            $totalSupplyCost = 0;

            foreach ($productInputs as $item) {
                $product = $products->where('id', $item['product_id'])->first();

                if (!$product) {
                    return $this->error([], "Product with ID {$item['product_id']} not found.", 422);
                }

                $quantity = $item['quantity'];

                if ($product->stock_quantity < $quantity) {
                    return $this->error([], "Not enough stock for {$product->name}.", 422);
                }

                $priceInCents = (int)($product->price * 100);
                $supplyPriceInCents = (int)($product->supply_price * 100);

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $priceInCents,
                        'product_data' => [
                            'name' => $product->name,
                        ],
                    ],
                    'quantity' => $quantity,
                ];

                $totalAmount += $priceInCents * $quantity;
                $totalSupplyCost += $supplyPriceInCents * $quantity;
            }

            // Calculate total application fee from totalAmount
            $applicationFeeAmount = (int)($totalAmount * ($percentageToTake / 100));

            $checkoutSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $user->email,
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel') . '?redirect_url=' . $request->get('cancel_redirect_url'),
                'payment_intent_data' => [
                    'application_fee_amount' => $applicationFeeAmount,
                    'transfer_data' => [
                        'destination' => $destination,
                    ],
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'store_id' => $request->store_id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address_id' => $request->address_id,
                    'products' => $productInputs->toJson(),
                    'success_redirect_url' => $request->success_redirect_url,
                    'cancel_redirect_url' => $request->cancel_redirect_url,
                ],
            ]);

            return $this->success($checkoutSession->url, 'Checkout session created successfully.', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function checkoutSuccess(Request $request)
    {
        if (!$request->query('session_id')) {
            return $this->error([], 'Session ID not found.', 200);
        }

        DB::beginTransaction();
        try {
            $sessionId = $request->query('session_id');
            $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);
            $metadata = $checkoutSession->metadata;

            $userId = $metadata->user_id ?? null;
            $storeId = $metadata->store_id ?? null;
            $productsJson = $metadata->products ?? null;
            $successUrl = $metadata->success_redirect_url ?? '/';
            $firstName = $metadata->first_name ?? '';
            $lastName = $metadata->last_name ?? '';
            $email = $metadata->email ?? '';
            $phone = $metadata->phone ?? '';
            $addressId = $metadata->address_id ?? null;

            if (!$userId || !$storeId || !$productsJson) {
                throw new \Exception('Invalid or missing metadata from Stripe session.');
            }

            $user = User::find($userId);
            if (!$user) {
                return $this->error([], 'User not found.', 404);
            }

            $productInputs = collect(json_decode($productsJson, true));
            $productIds = $productInputs->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->get();

            $totalAmount = 0;

            foreach ($productInputs as $item) {
                $product = $products->where('id', $item['product_id'])->first();
                if (!$product) {
                    throw new \Exception("Product ID {$item['product_id']} not found.");
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}.");
                }

                $totalAmount += $product->price * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => $user->id,
                'online_store_id' => $storeId,
                'address_id' => $addressId,
                'name' => $firstName . ' ' . $lastName,
                'email' => $email,
                'phone' => $phone,
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
            ]);

            foreach ($productInputs as $item) {
                $product = $products->where('id', $item['product_id'])->first();
                $quantity = $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                // Deduct stock
                $product->decrement('stock_quantity', $quantity);
            }

            DB::commit();
            return redirect($successUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function checkoutCancel(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect($request->redirect_url ?? '/');
        }

        try {
            $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);
            $metadata = $checkoutSession->metadata;

            $cancelUrl = $metadata->cancel_redirect_url ?? '/';

            return redirect($cancelUrl);
        } catch (\Exception $e) {
            return redirect('/');
        }
    }
}
