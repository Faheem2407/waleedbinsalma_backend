<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreCart;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    use ApiResponse;

    /**
     * Add or update product in cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $userId = Auth::id();

            $cartItem = StoreCart::updateOrCreate(
                [
                    'user_id' => $userId,
                    'online_store_id' => $request->online_store_id,
                    'product_id' => $request->product_id,
                ],
                [
                    'quantity' => $request->quantity,
                ]
            );

            return $this->success($cartItem->load('product'), 'Product added to cart.', 200);

        } catch (\Exception $e) {
            return $this->error('Failed to add to cart. ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove product from cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $userId = Auth::id();

            $cartItem = StoreCart::where('user_id', $userId)
                ->where('online_store_id', $request->online_store_id)
                ->where('product_id', $request->product_id)
                ->first();

            if (!$cartItem) {
                return $this->error('Cart item not found.', 404);
            }

            $cartItem->delete();

            return $this->success(null, 'Product removed from cart.', 200);

        } catch (\Exception $e) {
            return $this->error('Failed to remove product from cart. ' . $e->getMessage(), 500);
        }
    }

    /**
     * View all cart items for a store
     */
    public function view(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
        ]);

        try {
            $userId = Auth::id();

            $cartItems = StoreCart::with('product')
                ->where('user_id', $userId)
                ->where('online_store_id', $request->online_store_id)
                ->get();

            return $this->success($cartItems, 'Cart fetched successfully.', 200);

        } catch (\Exception $e) {
            return $this->error('Failed to fetch cart. ' . $e->getMessage(), 500);
        }
    }




    public function createCartPaymentIntent(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
        ]);

        $userId = Auth::id();

        $cartItems = StoreCart::with('product')
            ->where('user_id', $userId)
            ->where('online_store_id', $request->online_store_id)
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->error('Cart is empty.', 400);
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        if ($totalAmount <= 0) {
            return $this->error('Invalid total amount.', 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $totalAmount * 100, // cents
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'user_id' => $userId,
                'type' => 'cart_purchase'
            ]
        ]);

        return $this->success([
            'client_secret' => $intent->client_secret,
            'payment_intent_id' => $intent->id,
            'total' => $totalAmount
        ], 'PaymentIntent created.',201);
    }




    public function finalizeCartOrder(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'online_store_id' => 'required|exists:online_stores,id',
        ]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($intent->status !== 'succeeded') {
                return $this->error('Payment not successful.', 402);
            }

            $userId = Auth::id();

            DB::beginTransaction();

            $cartItems = StoreCart::with('product')
                ->where('user_id', $userId)
                ->where('online_store_id', $request->online_store_id)
                ->get();

            if ($cartItems->isEmpty()) {
                return $this->error('Cart is empty.', 400);
            }

            $totalAmount = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => $userId,
                'online_store_id' => $request->online_store_id,
                'total_amount' => $totalAmount,
                'payment_status' => 'succeeded',
                'payment_method' => 'stripe',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Clear Cart
            StoreCart::where('user_id', $userId)
                ->where('online_store_id', $request->online_store_id)
                ->delete();

            DB::commit();

            return $this->success($order->load('items.product'), 'Order placed successfully.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to finalize order. ' . $e->getMessage(), 500);
        }
    }

}

