<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
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
