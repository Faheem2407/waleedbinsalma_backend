<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

// $user = Auth::user();


class AppointmentReviewController extends Controller
{
    use ApiResponse;

public function submitReview(Request $request)
{
    $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
        'rating'         => 'required|integer|min:1|max:5',
        'review'         => 'nullable|string|max:1000',
    ]);

    $user = Auth::user();

    if (!$user) {
        return $this->error([], 'Unauthorized access. Please login first.', 401);
    }

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



}
