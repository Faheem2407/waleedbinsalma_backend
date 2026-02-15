<?php

namespace App\Http\Controllers\Api;

use Stripe\Stripe;
use Stripe\Account;
use App\Models\User;
use Stripe\AccountLink;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\BusinessBankDetails;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ConnectAccountController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function connectAccount(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            $user = auth()->user();


            if (!$user || !$user->businessProfile) {
                return $this->error([], 'User or Business Profile not found.', 200);
            }

            $businessProfile = $user->businessProfile;
            $bankDetail = $businessProfile->bankDetail;

            // Create bank detail if it doesn't exist
            if (!$bankDetail) {
                $bankDetail = $businessProfile->bankDetail()->create();
            }

            // Check if already connected
            if ($bankDetail->status === 'Enabled') {
                return $this->error([], 'Your account is already connected.', 200);
            }

            // Create Stripe account if not already created
            if (!$bankDetail->stripe_account_id) {
                $account = $stripe->accounts->create([
                    'type' => 'express',
                    'capabilities' => [
                        'transfers' => ['requested' => true],
                    ],
                ]);

                $bankDetail->update(['stripe_account_id' => $account->id]);

                $stripe->accounts->update($account->id, [
                    'settings' => [
                        'payouts' => [
                            'schedule' => [
                                'interval' => 'manual',
                            ],
                        ],
                    ],
                ]);
            } else {
                $account = $stripe->accounts->retrieve($bankDetail->stripe_account_id);

                $stripe->accounts->update($account->id, [
                    'settings' => [
                        'payouts' => [
                            'schedule' => [
                                'interval' => 'manual',
                            ],
                        ],
                    ],
                ]);
            }

            // If Stripe says payouts are enabled, mark as connected
            if ($account && $account->payouts_enabled) {
                $bankDetail->update(['status' => 'Enabled']);
                return $this->error([], 'Your account is already connected.', 200);
            }

            $successUrl = $request->success_redirect_url;
            $cancelUrl = $request->cancel_redirect_url;

            $accountLink = $stripe->accountLinks->create([
                'account' => $account->id,
                'refresh_url' => route('connect.cancel') . "?id={$account->id}&userId={$user->id}&success_redirect_url={$successUrl}&cancel_redirect_url={$cancelUrl}",
                'return_url' => route('connect.success') . "?id={$account->id}&userId={$user->id}&success_redirect_url={$successUrl}&cancel_redirect_url={$cancelUrl}",
                'type' => 'account_onboarding',
            ]);

            return $this->success(['url' => $accountLink->url], 'Redirecting to Stripe for account connection.', 200);
        } catch (\Exception $e) {
            return $this->error([], 'Error connecting account: ' . $e->getMessage(), 500);
        }
    }

    public function connectSuccess(Request $request)
    {
        $account = Account::retrieve($request->id);
        $user = User::find($request->get('userId'));

        Log::info('Connect Success', [
            'account_id' => $request->id,
            'user_id' => $request->get('userId'),
            'success_redirect_url' => $request->get('success_redirect_url'),
            'cancel_redirect_url' => $request->get('cancel_redirect_url'),
        ]);
        if (!$user || !$user->businessProfile || !$user->businessProfile->bankDetail) {
            return $this->error([], 'User or Bank Detail not found.', 404);
        }

        $bankDetail = $user->businessProfile->bankDetail;

        if (!$account->details_submitted || !$account->payouts_enabled) {
            $bankDetail->update(['status' => 'Rejected']);
            return redirect()->away($request->get('cancel_redirect_url'));
        }

        $bankDetail->update(['status' => 'Enabled']);
        return redirect()->away($request->get('success_redirect_url'));
    }

    public function connectCancel(Request $request)
    {
        Log::info('Connect Cancel', [
            'account_id' => $request->id,
            'user_id' => $request->userId,
            'success_redirect_url' => $request->success_redirect_url,
            'cancel_redirect_url' => $request->cancel_redirect_url,
        ]);
        if (!$request->id || !$request->userId || !$request->success_redirect_url || !$request->cancel_redirect_url) {
            return $this->error([], 'Missing required query parameters.', 400);
        }

        $link = \Stripe\AccountLink::create([
            'account' => $request->id,
            'refresh_url' => route('connect.cancel') . "?id={$request->id}&userId={$request->userId}&success_redirect_url={$request->success_redirect_url}&cancel_redirect_url={$request->cancel_redirect_url}",
            'return_url' => route('connect.success') . "?id={$request->id}&userId={$request->userId}&success_redirect_url={$request->success_redirect_url}&cancel_redirect_url={$request->cancel_redirect_url}",
            'type' => 'account_onboarding',
        ]);

        return redirect($link->url);
    }

    public function onboardVendor(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->businessProfile) {
            return response()->json(['status' => false, 'message' => 'User or Business Profile not found.'], 404);
        }

        $businessProfile = $user->businessProfile;
        try {
            $payload = [
                'name' => trim($businessProfile->business_name) ?: 'My Business',
                'legal_name' => $businessProfile->legal_name ?? $businessProfile->business_name, // often required
                'type' => 'CORPORATION',
                'commercial_registration_no' => $businessProfile->commercial_registration_no ?? '1234567890', // ← required for SA
                'contact_person' => [
                    'first_name' => $user->first_name ?? 'Business',
                    'last_name'  => $user->last_name ?? 'User',
                    'email'      => $user->email,
                    'phone'      => [
                        'country_code' => '966',
                        'number'       => ltrim($user->number ?? '138686959', '0'), // no leading zero
                    ],
                ],
                'address'               => [  // ← very often required
                    'line1'    => $businessProfile->address ?? 'Example Street 123',
                    'city'     => $businessProfile->city ?? 'Riyadh',
                    'state'    => $businessProfile->region ?? 'Riyadh Province',
                    'country'  => 'SA',
                    'zip'      => $businessProfile->postal_code ?? '11564',
                ],
                'settlement_currency' => 'SAR',  // instead of entities
                // If you have uploaded documents earlier, reference them:
                // 'documents' => ['commercial_registration' => 'file_id_from_upload'],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.tap_pay.private_key'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post('https://api.tap.company/v2/business', $payload);

            Log::info('Tap Business Request', ['body' => $payload]);
            Log::info('Tap Business Response', ['body' => $response->json(), 'status' => $response->status()]);

            $result = $response->json();

            log::info('Tap Business Result', ['result' => $result]);

            if ($response->successful() && isset($result['id'])) {
                BusinessBankDetails::updateOrCreate(
                    ['business_profile_id' => $businessProfile->id],
                    [
                        'tap_destination_id' => $result['id'],
                        'status' => $result['status'] === 'ACTIVE' ? 'Enabled' : 'Pending',
                    ]
                );

                return $this->success(['tap_destination_id' => $result['id']], 'Vendor onboarded successfully.', 200);
            }

            return $this->error([], 'Failed to onboard vendor: ' . ($result['message'] ?? 'Unknown error'), $response->status());
        } catch (\Exception $e) {
            Log::error('Tap Onboarding Exception', ['error' => $e->getMessage()]);
            return $this->error([], 'Failed to onboard vendor: ' . $e->getMessage(), 500);
        }
    }
}
