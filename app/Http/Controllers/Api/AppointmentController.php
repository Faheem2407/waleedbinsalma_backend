<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BusinessProfile;
use App\Models\CatalogService;
use App\Models\OnlineStore;
use App\Models\Payment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\StoreService;
use Illuminate\Support\Facades\Cache;
use Stripe\PaymentIntent;

class AppointmentController extends Controller
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
            'store_service_ids.*' => 'exists:store_services,catalog_service_id',
        ]);

        DB::beginTransaction();

        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->error([], 'User not authenticated.', 401);
                return $this->error([], 'User not authenticated.', 401);
            }

            // Calculate total price
            $services = CatalogService::whereIn('id', $request->store_service_ids)->get();


            $totalAmount = $services->sum('price');

            if ($totalAmount <= 0) {
                return $this->error([], 'Invalid amount for payment.', 400);
                return $this->error([], 'Invalid amount for payment.', 400);
            }

            $amountInCents = (int) ($totalAmount * 100);
            $applicationFeeAmount = (int) ($amountInCents * 0.05); // 5% fee

            $onlineStore = OnlineStore::findOrFail($request->online_store_id);

            $shopOwner = $onlineStore->businessProfile->bankDetail;

            if (!$shopOwner || !$shopOwner->stripe_account_id) {
                return $this->error([], 'Shop owner Stripe account not connected.', 400);
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'application_fee_amount' => $applicationFeeAmount,
                'transfer_data' => [
                    'destination' => $shopOwner->stripe_account_id,
                ],
                'metadata' => [
                    'user_id' => $userId,
                    'online_store_id' => $onlineStore->id,
                    'appointment_type' => $request->appointment_type,
                ],
            ]);

            // DB::commit();

            return $paymentIntent;
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 'Failed to book appointment', 500);
        }
    }

    public function myAppointments()
    {
        $userId = Auth::id();
        $now = now();

        $upcoming = Appointment::with(['storeServices.service', 'onlineStore'])
            ->where('user_id', $userId)
            ->where(function ($query) use ($now) {
                $query->where('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', $now->toDateString())
                            ->where('time', '>=', $now->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $previous = Appointment::with(['storeServices.service', 'onlineStore'])
            ->where('user_id', $userId)
            ->where(function ($query) use ($now) {
                $query->where('date', '<', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', $now->toDateString())
                            ->where('time', '<', $now->format('H:i:s'));
                    });
            })
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        $data = [
            'upcoming_appointments' => $upcoming,
            'previous_appointments' => $previous,
        ];

        return $this->success($data, 'Appointments fetched successfully.', 200);
    }



    public function rescheduleAppointment(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$appointment) {
            return $this->error([], 'Appointment not found.', 404);
        }

        // Optional: Check if already cancelled or past
        if ($appointment->status === 'cancelled') {
            return $this->error([], 'Cannot reschedule a cancelled appointment.', 400);
        }

        $appointment->update([
            'date' => $request->date,
            'time' => $request->time,
        ]);

        $data = $appointment->load('storeServices.service');

        return $this->success($data, 'Appointment rescheduled successfully.', 200);
    }



    public function cancelAppointment($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$appointment) {
            return $this->error('Appointment not found.', 404);
        }

        if ($appointment->status === 'cancelled') {
            return $this->error([], 'Appointment already cancelled.', 400);
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return $this->success([], 'Appointment cancelled successfully.', 200);
    }
}
