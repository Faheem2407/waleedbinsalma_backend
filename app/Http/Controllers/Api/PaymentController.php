<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscription;
use App\Models\OnlineStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function checkout(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'store_id' => 'required|exists:online_stores,id',
            'success_redirect_url' => 'required|url',
            'cancel_redirect_url' => 'required|url',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validated->errors(),
                'message' => $validated->errors()->first()
            ], 422);
        }

        $store = OnlineStore::findOrFail($request->store_id);
        $price = 999; // $9.99 in cents

        try {
            $checkoutSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $price,
                        'product_data' => [
                            'name' => 'Store Promotion Subscription (30 Days)',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => auth()->user()->email,
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}&store_id=' . $store->id . '&redirect=' . urlencode($request->success_redirect_url),
                'cancel_url' => route('checkout.cancel') . '?redirect=' . urlencode($request->cancel_redirect_url),
                'metadata' => [
                    'store_id' => $store->id,
                ],
            ]);

            return response()->json([
                'status' => true,
                'checkout_url' => $checkoutSession->url,
                'message' => 'Checkout session created successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        $storeId = $request->query('store_id');
        $redirect = $request->query('redirect');

        if (!$sessionId || !$storeId) {
            return response()->json([
                'status' => false,
                'message' => 'Missing session or store info.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);
            $store = OnlineStore::findOrFail($storeId);

            $now = now();
            $end = $now->copy()->addDays(30);

            // Save payment info
            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'payment_intent_id' => $session->payment_intent,
                'amount' => $session->amount_total,
                'currency' => $session->currency,
                'status' => $session->payment_status,
                'payment_method' => $session->payment_method_types[0] ?? null,
            ]);

            Subscription::create([
                'online_store_id' => $store->id,
                'start_date' => $now,
                'end_date' => $end,
                'is_renew' => false,
            ]);

            DB::commit();

            return redirect($redirect ?? '/');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkoutCancel(Request $request)
    {
        $redirect = $request->query('redirect') ?? '/';

        return redirect($redirect);
    }
}
