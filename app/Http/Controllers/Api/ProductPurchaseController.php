<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineStore;
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
                'success_url' => $request->success_redirect_url . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->cancel_redirect_url,
                'payment_intent_data' => [
                    'application_fee_amount' => $applicationFeeAmount,
                    'transfer_data' => [
                        'destination' => $destination,
                    ],
                ],
                'metadata' => [
                    'user_id' => $user->id,
                    'store_id' => $request->store_id,
                    'products' => $productIds,
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

            $success_redirect_url = $metadata->success_redirect_url ?? null;
            $user_id = $metadata->user_id ?? null;
            $movement_id = $metadata->movement_id ?? null;
            $movement_title = $metadata->movement_title ?? null;
            $amount = $metadata->amount ?? null;

            $user = User::find($user_id);

            if (!$user) {
                return $this->error([], 'User not found.', 200);
            }

            $donationHistory = DonationHistory::create([
                'user_id' => $user_id,
                'movement_id' => $movement_id,
                'amount' => $amount,
            ]);

            DB::commit();
            return redirect($success_redirect_url);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }

    // public function checkoutCancel(Request $request)
    // {
    //     $sessionId = $request->query('session_id');

    //     if (!$sessionId) {
    //         return redirect($request->redirect_url ?? null);
    //     }

    //     $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);
    //     $metadata = $checkoutSession->metadata;

    //     $cancel_redirect_url = $metadata->cancel_redirect_url ?? null;

    //     return redirect($cancel_redirect_url);
    // }
}
