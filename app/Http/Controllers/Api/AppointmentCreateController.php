<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CatalogService;
use App\Models\OnlineStore;
use App\Models\Payment;
use App\Models\StoreService;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Mail\AppointmentConfirmation;
use Illuminate\Support\Facades\Mail;

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
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'booking_notes' => 'required|string',
            'store_service_ids' => 'required|array|min:1',
            'store_service_ids.*' => 'exists:catalog_services,id',
            'success_redirect_url' => 'nullable|url',
            'cancel_redirect_url' => 'nullable|url',
            'discount_code' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error([], 'User not authenticated.', 401);
            }

            /**
             * === Prevent booking at same date and time ===
             */
            $existingAppointment = Appointment::where('online_store_id', $request->online_store_id)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->where('status', 'confirmed')
                ->first();

            if ($existingAppointment) {
                return $this->error([], 'This appointment slot is already booked.', 409);
            }

            $services = CatalogService::whereIn('id', $request->store_service_ids)->get();
            if ($services->isEmpty()) {
                return $this->error([], 'No valid services found.', 400);
            }

            $totalAmount = $services->sum('price');
            $discountAmount = 0;
            $discountCode = null;

            // Validate and apply discount code if provided
            if ($request->discount_code) {
                $discountCode = DiscountCode::where('online_store_id', $request->online_store_id)
                    ->where('code', $request->discount_code)
                    ->first();

                if (!$discountCode) {
                    return $this->error([], 'Invalid discount code.', 400);
                }

                if (!$discountCode->isValid($totalAmount)) {
                    return $this->error([], 'Discount code is not valid or has expired.', 400);
                }

                $discountAmount = $discountCode->calculateDiscount($totalAmount);
                $totalAmount -= $discountAmount;
            }

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
                'customer_email' => $user->email,
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'sar',
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
                    'user_id' => $user->id,
                    'appointment_type' => $request->appointment_type,
                    'date' => $request->date,
                    'time' => $request->time,
                    'booking_notes' => $request->booking_notes,
                    'store_service_ids' => implode(',', $request->store_service_ids),
                    'success_redirect_url' => $request->get('success_redirect_url'),
                    'cancel_redirect_url' => $request->get('cancel_redirect_url'),
                    'discount_code_id' => $discountCode ? $discountCode->id : null,
                    'discount_amount_applied' => $discountAmount,
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
            Log::info('Stripe Session Metadata:', (array) $metadata);
            $success_redirect_url = $metadata->success_redirect_url ?? null;

            $userId = $metadata['user_id'];
            $storeServiceIds = explode(',', $metadata['store_service_ids']);
            $totalAmount = (int) ($session->amount_total / 100);
            $discountCodeId = $metadata->discount_code_id ?? null;
            $discountAmount = $metadata->discount_amount_applied ?? 0;

            DB::beginTransaction();

            $appointment = Appointment::create([
                'online_store_id' => $metadata['online_store_id'],
                'user_id' => $userId,
                'appointment_type' => $metadata['appointment_type'],
                'date' => $metadata['date'],
                'time' => $metadata['time'],
                'booking_notes' => $metadata['booking_notes'],
                'status' => 'confirmed',
                'discount_code_id' => $discountCodeId,
                'discount_amount_applied' => $discountAmount,
            ]);

            // Increment discount code usage if applied
            if ($discountCodeId) {
                $discountCode = DiscountCode::find($discountCodeId);
                if ($discountCode) {
                    $discountCode->increment('used_count');
                    if ($discountCode->usage_limit && $discountCode->used_count >= $discountCode->usage_limit) {
                        $discountCode->update(['is_active' => false]);
                    }
                }
            }

            $storeServiceIdsMapped = [];
            $services = [];
            foreach ($storeServiceIds as $catalogId) {
                $storeService = StoreService::where('catalog_service_id', $catalogId)
                    ->where('online_store_id', $metadata['online_store_id'])
                    ->first();

                if ($storeService) {
                    $storeServiceIdsMapped[] = $storeService->id;
                    $services[] = [
                        'name' => $storeService->catalogService->name,
                        'price' => $storeService->catalogService->price,
                    ];
                }
            }

            $appointment->storeServices()->attach($storeServiceIdsMapped);

            Payment::create([
                'user_id' => $userId,
                'appointment_id' => $appointment->id,
                'amount' => $totalAmount,
                'currency' => 'sar',
                'status' => 'succeeded',
                'payment_method' => 'stripe',
                'payment_intent_id' => $session->payment_intent,
            ]);

            // Send appointment confirmation email
            $user = User::findOrFail($userId);
            $store = OnlineStore::findOrFail($metadata['online_store_id']);
            try {
                Mail::to($user->email)->send(new AppointmentConfirmation(
                    $user,
                    $store,
                    $appointment,
                    $services,
                    $totalAmount,
                    false,
                    $discountCode ? [
                        'code' => $discountCode->code,
                        'amount' => $discountAmount,
                    ] : null
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send appointment confirmation email: ', [
                    'error' => $e->getMessage(),
                    'user_id' => $userId,
                    'appointment_id' => $appointment->id,
                ]);
            }

            DB::commit();

            return redirect($success_redirect_url);

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

    public function downloadInvoice($appointmentId)
    {
        $appointment = Appointment::with([
            'user',
            'onlineStore',
            'storeServices.catalogService',
            'payments',
            'discountCode',
        ])->find($appointmentId);

        if (!$appointment) {
            return $this->error([], 'No appointment found', 404);
        }

        $invoiceNumber = 'INV-' . str_pad($appointment->id, 6, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd');

        $services = $appointment->storeServices->map(function ($storeService) {
            $catalogService = $storeService->catalogService;
            return [
                'name' => $catalogService->name,
                'description' => $catalogService->description ?? 'N/A',
                'duration' => $catalogService->duration ?? 'N/A',
                'price' => $catalogService->price,
            ];
        });

        $subtotal = $services->sum('price');
        $discountAmount = $appointment->discount_amount_applied ?? 0;
        $totalAmount = $appointment->payments->isNotEmpty()
            ? $appointment->payments->sum('amount')
            : ($subtotal - $discountAmount);

        $data = [
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now()->format('Y-m-d'),
            'appointment' => $appointment,
            'customer' => $appointment->user,
            'services' => $services,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'discount_code' => $appointment->discountCode ? $appointment->discountCode->code : null,
            'total_amount' => $totalAmount,
            'store' => $appointment->onlineStore,
            'payment' => $appointment->payments->first(),
        ];

        try {
            $pdf = Pdf::loadView('invoices.appointment', $data);
            return $pdf->download("invoice-{$invoiceNumber}.pdf");
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            return $this->error([], 'Failed to generate invoice.', 500);
        }
    }
}