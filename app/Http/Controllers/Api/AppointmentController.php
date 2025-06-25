<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CatalogService;
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
            'store_service_ids.*' => 'exists:store_services,id',
            'stripe_token' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->error([], 'User not authenticated.', 401);
            }

            $storeServices = StoreService::with('catalogService')
                ->whereIn('id', $request->store_service_ids)
                ->get();

            $totalAmount = $storeServices->sum(fn($ss) => $ss->catalogService->price ?? 0);

            if ($totalAmount <= 0) {
                return $this->error([], 'Invalid amount for payment.', 400);
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $charge = Charge::create([
                'amount' => (int) ($totalAmount * 100),
                'currency' => 'usd',
                'source' => $request->stripe_token,
                'description' => 'Appointment payment',
                'metadata' => [
                    'user_id' => $userId,
                    'appointment_type' => $request->appointment_type,
                ],
            ]);

            if ($charge->status !== 'succeeded') {
                DB::rollBack();
                return $this->error([], 'Payment failed.', 402);
            }

            $appointment = Appointment::create([
                'online_store_id' => $request->online_store_id,
                'user_id' => $userId,
                'appointment_type' => $request->appointment_type,
                'is_professional_selected' => $request->boolean('is_professional_selected'),
                'date' => $request->date,
                'time' => $request->time,
                'booking_notes' => $request->booking_notes,
                'status' => 'confirmed',
            ]);

            Payment::create([
                'user_id' => $userId,
                'appointment_id' => $appointment->id,
                'amount' => $totalAmount,
                'currency' => 'usd',
                'status' => 'succeeded',
                'payment_method' => 'stripe',
                'payment_intent_id' => $charge->id,
            ]);

            $appointment->storeServices()->attach($request->store_service_ids);

            DB::commit();

            $data = $appointment->load('storeServices.service');

            return $this->success($data, 'Appointment booked and payment successful.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 'Failed to book appointment.', 500);
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

        return $this->success($data, 'Appointments fetched successfully.',200);
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
            return $this->error([],'Appointment not found.', 404);
        }

        // Optional: Check if already cancelled or past
        if ($appointment->status === 'cancelled') {
            return $this->error([],'Cannot reschedule a cancelled appointment.', 400);
        }

        $appointment->update([
            'date' => $request->date,
            'time' => $request->time,
        ]);

        $data = $appointment->load('storeServices.service');

        return $this->success($data, 'Appointment rescheduled successfully.',200);
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
            return $this->error([],'Appointment already cancelled.', 400);
        }

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return $this->success([], 'Appointment cancelled successfully.',200);
    }


}
