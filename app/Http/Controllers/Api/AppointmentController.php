<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentCancellation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\OnlineStore;
use App\Models\User;
use App\Models\AppointmentTeamAssignment;
use App\Models\Team;
use App\Mail\TeamAppointmentNotification;
use Illuminate\Support\Facades\DB;


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

        $upcoming = Appointment::with(['storeServices.catalogService', 'onlineStore'])
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

        $previous = Appointment::with(['storeServices.catalogService', 'onlineStore'])
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

        // Check if already cancelled
        if ($appointment->status === 'cancelled') {
            return $this->error([], 'Cannot reschedule a cancelled appointment.', 400);
        }

        // Check if the new slot is already booked
        $existingAppointment = Appointment::where('online_store_id', $appointment->online_store_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('status', 'confirmed')
            ->where('id', '!=', $id)
            ->first();

        if ($existingAppointment) {
            return $this->error([], 'This appointment slot is already booked.', 409);
        }

        DB::beginTransaction();

        try {
            // Check if the appointment has a declined team assignment
            $teamAssignment = AppointmentTeamAssignment::where('appointment_id', $appointment->id)
                ->where('team_member_status', 'declined')
                ->first();

            $newTeamId = null;
            if ($teamAssignment) {
                // Find a new team member who can provide the services and is available
                $storeServiceIds = $appointment->storeServices()->pluck('catalog_service_id')->toArray();
                $newTeam = Team::whereNotIn('id', [$teamAssignment->team_id])
                    ->whereHas('catalogServices', function ($query) use ($storeServiceIds) {
                        $query->whereIn('catalog_services.id', $storeServiceIds);
                    })
                    ->whereDoesntHave('appointmentAssignments', function ($query) use ($request) {
                        $query->where('team_member_status', 'accepted')
                            ->whereHas('appointment', function ($subQuery) use ($request) {
                                $subQuery->where('date', $request->date)
                                    ->where('time', $request->time)
                                    ->where('status', 'confirmed');
                            });
                    })
                    ->first();

                if (!$newTeam) {
                    DB::rollBack();
                    return $this->error([], 'No available team member found for the selected services and time slot.', 400);
                }

                // Update or create new team assignment
                $newTeamId = $newTeam->id;
                AppointmentTeamAssignment::create([
                    'appointment_id' => $appointment->id,
                    'team_id' => $newTeam->id,
                    'team_member_status' => 'pending',
                ]);

                // $teamAssignment->update(['team_member_status' => 'inactive']);
            }

            // Update appointment details
            $appointment->update([
                'date' => $request->date,
                'time' => $request->time,
                'status' => $newTeamId ? 'pending' : 'confirmed', 
            ]);

            // Send rescheduling confirmation email to user
            $user = User::findOrFail($appointment->user_id);
            $store = OnlineStore::findOrFail($appointment->online_store_id);
            $services = $appointment->storeServices->map(function ($storeService) {
                return [
                    'name' => $storeService->catalogService->name,
                    'price' => $storeService->catalogService->price,
                ];
            })->toArray();
            $totalAmount = $appointment->payments->isNotEmpty()
                ? $appointment->payments->sum('amount')
                : collect($services)->sum('price');

            try {
                Mail::to($user->email)->send(new AppointmentConfirmation($user, $store, $appointment, $services, $totalAmount, true));
            } catch (\Exception $e) {
                Log::error('Failed to send rescheduling confirmation email: ', [
                    'error' => $e->getMessage(),
                    'user_id' => $appointment->user_id,
                    'appointment_id' => $appointment->id,
                ]);
            }

            // Send notification to new team member if assigned
            if ($newTeamId) {
                try {
                    $newTeamMember = Team::findOrFail($newTeamId);
                    Mail::to($newTeamMember->email)->send(new TeamAppointmentNotification(
                        $appointment,
                        $newTeamMember,
                        $store,
                        $services
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send team appointment notification email: ', [
                        'error' => $e->getMessage(),
                        'team_id' => $newTeamId,
                        'appointment_id' => $appointment->id,
                    ]);
                }
            }

            DB::commit();

            $data = $appointment->load('storeServices.catalogService');

            return $this->success($data, 'Appointment rescheduled successfully.', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 'Failed to reschedule appointment.', 500);
        }
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

        // Send cancellation confirmation email
        try {
            $user = User::findOrFail($appointment->user_id);
            $store = OnlineStore::findOrFail($appointment->online_store_id);
            Mail::to($user->email)->send(new AppointmentCancellation($user, $store, $appointment));
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation confirmation email: ', [
                'error' => $e->getMessage(),
                'user_id' => $appointment->user_id,
                'appointment_id' => $appointment->id,
            ]);
        }

        return $this->success([], 'Appointment cancelled successfully.', 200);
    }

    public function totalAppointmentsThisWeek()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $totalAppointments = Appointment::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->count();

        $data = [
            'total_appointment_this_week' => $totalAppointments
        ];
        
        return $this->success($data, 'total appointment count fetched successfully', 200);
    }
}