<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnlineStore;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Carbon\Carbon;
use Stripe\Stripe;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class SubscriptionController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
            'success_redirect_url' => 'required|url',
            'cancel_redirect_url' => 'required|url',
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error([],'user not authenticated',401);
            }

            $store = OnlineStore::findOrFail($request->online_store_id);
            $amountInCents = 999;

            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amountInCents,
                        'product_data' => [
                            'name' => '30-Day Subscription: ' . ($store->name ?? 'Online Store'),
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'online_store_id' => $store->id,
                    'user_id' => $user->id,
                    'success_redirect_url' => $request->success_redirect_url,
                    'cancel_redirect_url' => $request->cancel_redirect_url,
                ],
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.cancel') . '?redirect_url=' . $request->cancel_redirect_url,
            ]);

            return response()->json([
                'redirect_url' => $checkoutSession->url
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(),'Failed to create stripe session',500);
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return response()->json(['message' => 'Missing session ID.'], 400);
        }

        try {
            $session = Session::retrieve($sessionId);
            $meta = $session->metadata;
            $storeId = $meta['online_store_id'];
            $redirectUrl = $meta['success_redirect_url'] ?? '/';

            $now = now();
            $end = $now->copy()->addDays(30);

            DB::beginTransaction();

            $subscription = Subscription::create([
                'online_store_id' => $storeId,
                'start_date' => $now,
                'end_date' => $end,
                'is_renew' => false,
            ]);

            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'payment_intent_id' => $session->payment_intent,
                'amount' => $session->amount_total,
                'currency' => $session->currency,
                'status' => $session->payment_status,
                'payment_method' => $session->payment_method_types[0] ?? null,
            ]);

            DB::commit();

            return redirect($redirectUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to complete subscription.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request)
    {
        $redirectUrl = $request->query('redirect_url') ?? '/';
        return redirect($redirectUrl);
    }


public function renew(Request $request)
{
    $request->validate([
        'online_store_id' => 'required|exists:online_stores,id',
    ]);

    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }

    $store = OnlineStore::findOrFail($request->online_store_id);

    $latestSubscription = Subscription::where('online_store_id', $store->id)
        ->latest('end_date')
        ->first();

    if (!$latestSubscription || Carbon::parse($latestSubscription->end_date)->isFuture()) {
        return response()->json([
            'status' => false,
            'message' => 'Subscription is still active or does not exist.',
        ], 400);
    }

    // Create a new subscription period
    $now = now();
    $end = $now->copy()->addDays(30);

    $renewed = Subscription::create([
        'online_store_id' => $store->id,
        'start_date' => $now,
        'end_date' => $end,
        'is_renew' => true,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Subscription renewed successfully.',
        'data' => $renewed
    ], 201);
}


}
