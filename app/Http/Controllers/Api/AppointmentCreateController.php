<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CatalogService;
use App\Models\OnlineStore;
use App\Models\Payment;
use App\Models\StoreService;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class AppointmentCreateController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function bookAppointment(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
            'appointment_type' => 'required|in:single,group',
            'is_professional_selected' => 'required|accepted',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'booking_notes' => 'required|string',
            'store_service_ids' => 'required|array|min:1',
            'store_service_ids.*' => 'exists:catalog_services,id',
            'success_redirect_url' => 'nullable|url',
            'cancel_redirect_url' => 'nullable|url',
        ]);

        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->error([], 'User not authenticated.', 401);
            }

            $services = CatalogService::whereIn('id', $request->store_service_ids)->get();
            if ($services->isEmpty()) {
                return $this->error([], 'No valid services found.', 400);
            }

            $totalAmount = $services->sum('price');
            if ($totalAmount <= 0) {
                return $this->error([], 'Invalid amount for payment.', 400);
            }

            $amountInCents = (int) ($totalAmount * 100);
            $applicationFeeAmount = (int) ($amountInCents * 0.05);

            $onlineStore = OnlineStore::findOrFail($request->online_store_id);
            $shopOwner = $onlineStore->businessProfile->bankDetail;

            if (!$shopOwner || !$shopOwner->stripe_account_id) {
                return $this->error([], 'Shop owner Stripe account not connected.', 400);
            }

            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $amountInCents,
                        'product_data' => [
                            'name' => 'Appointment Booking at ' . $onlineStore->name,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'payment_intent_data' => [
                    'application_fee_amount' => $applicationFeeAmount,
                    'transfer_data' => [
                        'destination' => $shopOwner->stripe_account_id,
                    ],
                ],
                'metadata' => [
                    'online_store_id' => $request->online_store_id,
                    'user_id' => $userId,
                    'appointment_type' => $request->appointment_type,
                    'is_professional_selected' => $request->boolean('is_professional_selected'),
                    'date' => $request->date,
                    'time' => $request->time,
                    'booking_notes' => $request->booking_notes,
                    'store_service_ids' => implode(',', $request->store_service_ids),
                ],
                'success_url' => route('appointment.book.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('appointment.book.cancel') . '?redirect_url=' . $request->get('cancel_redirect_url'),
            ]);

            return $this->success([
                'redirect_url' => $checkoutSession->url
            ], 'Redirecting to Stripe Checkout...', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 'Failed to book appointment.', 500);
        }
    }

    public function bookAppointmentSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return $this->error([], 'Missing session ID.', 400);
        }

        try {
            $session = Session::retrieve($sessionId);
            $metadata = $session->metadata;

            $userId = $metadata['user_id'];
            $storeServiceIds = explode(',', $metadata['store_service_ids']);
            $totalAmount = (int) ($session->amount_total / 100);

            DB::beginTransaction();

            $appointment = Appointment::create([
                'online_store_id' => $metadata['online_store_id'],
                'user_id' => $userId,
                'appointment_type' => $metadata['appointment_type'],
                'is_professional_selected' => $metadata['is_professional_selected'],
                'date' => $metadata['date'],
                'time' => $metadata['time'],
                'booking_notes' => $metadata['booking_notes'],
                'status' => 'confirmed',
            ]);

            $storeServiceIdsMapped = [];
            foreach ($storeServiceIds as $catalogId) {
                $storeService = StoreService::where('catalog_service_id', $catalogId)
                    ->where('online_store_id', $metadata['online_store_id'])
                    ->first();

                if ($storeService) {
                    $storeServiceIdsMapped[] = $storeService->id;
                }
            }

            $appointment->storeServices()->attach($storeServiceIdsMapped);

            Payment::create([
                'user_id' => $userId,
                'appointment_id' => $appointment->id,
                'amount' => $totalAmount,
                'currency' => 'usd',
                'status' => 'pending',
                'payment_method' => 'stripe',
                'payment_intent_id' => $session->payment_intent,
            ]);

            DB::commit();

            return $this->success($appointment, 'Appointment confirmed and payment completed.', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 'Failed to complete appointment.', 500);
        }
    }

    public function bookAppointmentCancel(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect($request->redirect_url ?? null);
        }

        $checkoutSession = \Stripe\Checkout\Session::retrieve($sessionId);
        $metadata = $checkoutSession->metadata;

        $cancel_redirect_url = $metadata->cancel_redirect_url ?? null;

        return redirect($cancel_redirect_url);
    }
}
