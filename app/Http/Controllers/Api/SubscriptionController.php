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
use App\Traits\ApiResponse;
use App\Models\SubscriptionPrice;

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
                return $this->error([], 'User not authenticated', 401);
            }

            $store = OnlineStore::findOrFail($request->online_store_id);

            // Check if there's an active (non-expired) subscription
            $activeSubscription = Subscription::where('online_store_id', $store->id)
                ->where('end_date', '>=', now())
                ->latest('end_date')
                ->first();

            if ($activeSubscription) {
                return $this->error([], 'Subscription already active until ' . Carbon::parse($activeSubscription->end_date)->toDateString(), 400);
            }

            $priceModel = SubscriptionPrice::first();

            if (!$priceModel) {
                return $this->error([], 'Subscription price not set in the system.', 500);
            }

            $amountInCentsForSubscription = intval($priceModel->price * 100);

            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amountInCentsForSubscription,
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

            return $this->success(['redirect_url' => $checkoutSession->url], 'Checkout URL created');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'Failed to create Stripe session', 500);
        }
    }

    public function handleSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->error([], 'Missing session ID.', 400);
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
            return $this->error([], 'Failed to complete subscription: ' . $e->getMessage(), 500);
        }
    }

    public function handleCancel(Request $request)
    {
        $redirectUrl = $request->query('redirect_url') ?? '/';
        return redirect($redirectUrl);
    }



    public function renew(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
            'success_redirect_url' => 'required|url',
            'cancel_redirect_url' => 'required|url',
        ]);

        $user = Auth::user();
        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $store = OnlineStore::findOrFail($request->online_store_id);

        $latestSubscription = Subscription::where('online_store_id', $store->id)
            ->latest('end_date')
            ->first();

        if (!$latestSubscription || Carbon::parse($latestSubscription->end_date)->isFuture()) {
            return $this->error([], 'Subscription is still active or not found.', 400);
        }

        try {
            $priceModel = SubscriptionPrice::first();

            if (!$priceModel) {
                return $this->error([], 'Subscription price not set in the system.', 500);
            }

            $amountInCentsForRenewSubscription = intval($priceModel->price * 100);

            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amountInCentsForRenewSubscription,
                        'product_data' => [
                            'name' => 'Renewal: 30-Day Subscription - ' . ($store->name ?? 'Online Store'),
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'renew_subscription_id' => $latestSubscription->id,
                    'success_redirect_url' => $request->success_redirect_url,
                    'cancel_redirect_url' => $request->cancel_redirect_url,
                ],
                'success_url' => route('subscription.renew.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.cancel') . '?redirect_url=' . $request->cancel_redirect_url,
            ]);

            return $this->success([
                'redirect_url' => $checkoutSession->url
            ], 'Stripe checkout for renewal created.');
        } catch (\Exception $e) {
            return $this->error([], 'Stripe error: ' . $e->getMessage(), 500);
        }
    }





    public function handleRenewSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return $this->error([], 'Missing session ID.', 400);
        }

        try {
            $session = Session::retrieve($sessionId);
            $meta = $session->metadata;

            $subscriptionId = $meta['renew_subscription_id'] ?? null;
            $redirectUrl = $meta['success_redirect_url'] ?? '/';

            if (!$subscriptionId) {
                return $this->error([], 'Subscription ID missing in metadata.', 400);
            }

            $subscription = Subscription::findOrFail($subscriptionId);

            $now = now();
            $end = $now->copy()->addDays(30);

            DB::beginTransaction();

            $subscription->update([
                'start_date' => $now,
                'end_date' => $end,
                'is_renew' => true,
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
            return $this->error([], 'Failed to complete renewal: ' . $e->getMessage(), 500);
        }
    }


}
