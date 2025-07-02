<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\OnlineStore;


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

    public function storeReviews(Request $request)
    {
        $request->validate([
            'online_store_id' => 'required|exists:online_stores,id',
            'rating' => 'nullable|in:1,2,3,4,5,all',
            'search' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthorized access. Please login first.', 401);
        }

        $store = OnlineStore::with('businessProfile')->find($request->online_store_id);

        if (!$store || !$store->businessProfile || $store->businessProfile->user_id !== $user->id) {
            return $this->error([], 'You do not have permission to view reviews for this store.', 403);
        }

        $query = Review::with([
            'user:id,first_name,last_name,email,avatar',
            'appointment.appointmentServices.catalogService:id,name,price,duration'
        ])
            ->where('online_store_id', $store->id);

        // Filter by rating if provided
        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', $request->rating);
        }

        // Filter by user name (who gave the review)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                });
            });
        }


        $reviews = $query->latest()->get();

        return $this->success($reviews, 'Filtered store reviews fetched successfully.', 200);
    }
}
