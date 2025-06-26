<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Appointment;
use App\Traits\ApiResponse;

class AppointmentReviewController extends Controller
{
    use ApiResponse;

    public function submitReview(Request $request)
    {
        $request->validate([
            'appointment_id'   => 'required|exists:appointments,id',
            'rating'           => 'required|integer|min:1|max:5',
            'review'           => 'nullable|string|max:1000',
        ]);

        $user = auth->user();

        $appointment = Appointment::with('onlineStore')
            ->where('id', $request->appointment_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$appointment) {
            return $this->error([], 'Unauthorized or invalid appointment.', 403);
        }

        if ($appointment->review) {
            return $this->error([], 'Review already submitted for this appointment.', 409);
        }

        $review = Review::create([
            'user_id'         => $user->id,
            'appointment_id'  => $appointment->id,
            'online_store_id' => $appointment->online_store_id,
            'rating'          => $request->rating,
            'review'          => $request->review,
        ]);

        return $this->success($review, 'Review submitted successfully.', 201);
    }



//     public function submitReview(Request $request)
// {
//     $request->validate([
//         'appointment_id' => 'required|exists:appointments,id',
//         'rating'         => 'required|integer|min:1|max:5',
//         'review'         => 'nullable|string|max:1000',
//     ]);

//     $user = auth()->user();

//     $appointment = Appointment::where('id',$request->appointment_id)->first();

//     // 👇 THIS CHECK MUST COME BEFORE ANY USAGE OF $appointment
//     if (!$appointment) {
//         return $this->error([], 'Unauthorized or invalid appointment.', 403);
//     }

//     // Prevent duplicate review
//     if ($appointment->review) {
//         return $this->error([], 'Review already submitted for this appointment.', 409);
//     }

//     // Safe to access $appointment->id and $appointment->online_store_id now
//     $review = Review::create([
//         'user_id'         => $user->id,
//         'appointment_id'  => $appointment->id,
//         'online_store_id' => $appointment->online_store_id,
//         'rating'          => $request->rating,
//         'review'          => $request->review,
//     ]);

//     return $this->success($review, 'Review submitted successfully.', 201);
// }

}
