<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;

class BusinessOwnerDashboardController extends Controller
{
	use ApiResponse;
	// public function appointmentAnalytics(Request $request)
	// {
	// 	$storeId = $request->input('online_store_id');

	// 	if (!$storeId) {
	// 		return $this->error([], 'Online store ID is required.', 422);
	// 	}

	// 	$today = now()->startOfDay();
	// 	$next7Days = now()->addDays(7)->endOfDay();
	// 	$next30Days = now()->addDays(30)->endOfDay();
	// 	$startOfMonth = now()->startOfMonth();
	// 	$startOfLastMonth = now()->subMonth()->startOfMonth();
	// 	$endOfLastMonth = now()->subMonth()->endOfMonth();

		
	// 	$appointmentIds = Appointment::where('online_store_id', $storeId)->pluck('id');

	// 	$appointmentsQuery = Appointment::where('online_store_id', $storeId);

	// 	$totalAppointments = $appointmentIds->count();
	// 	$totalConfirmed = Appointment::whereIn('id', $appointmentIds)->where('status', 'confirmed')->count();
	// 	$totalCanceled = Appointment::whereIn('id', $appointmentIds)->where('status', 'canceled')->count();

	// 	$totalPrice = Payment::whereIn('appointment_id', $appointmentIds)
	// 		->where('status', 'succeeded')
	// 		->sum('amount');

	// 	$next7DaysAppointments = (clone $appointmentsQuery)
	// 		->whereBetween('date', [$today, $next7Days])
	// 		->orderBy('date')
	// 		->get();

	// 	$next30DaysAppointments = (clone $appointmentsQuery)
	// 		->whereBetween('date', [$today, $next30Days])
	// 		->orderBy('date')
	// 		->get();

	// 	$todaysAppointments = (clone $appointmentsQuery)
	// 		->whereDate('date', now()->toDateString())
	// 		->orderBy('time')
	// 		->get();

	// 	$appointmentActivities = (clone $appointmentsQuery)
	// 		->with(['user', 'storeServices'])
	// 		->orderBy('created_at', 'desc')
	// 		->take(20)
	// 		->get();

	// 	$topServicesThisMonth = Appointment::where('online_store_id', $storeId)
	// 	    ->whereBetween('date', [$startOfMonth, now()])
	// 	    ->with('storeServices.service') // eager load service relation
	// 	    ->get()
	// 	    ->flatMap->storeServices
	// 	    ->groupBy('id')
	// 	    ->map(function ($services) {
	// 	        return [
	// 	            'service_name' => optional($services->first()->service)->service_name ?? 'Unknown',
	// 	            'count' => $services->count(),
	// 	        ];
	// 	    })
	// 	    ->sortByDesc('count')
	// 	    ->values()
	// 	    ->take(5);

	// 	$topServicesLastMonth = Appointment::where('online_store_id', $storeId)
	// 	    ->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
	// 	    ->with('storeServices.service')
	// 	    ->get()
	// 	    ->flatMap->storeServices
	// 	    ->groupBy('id')
	// 	    ->map(function ($services) {
	// 	        return [
	// 	            'service_name' => optional($services->first()->service)->service_name ?? 'Unknown',
	// 	            'count' => $services->count(),
	// 	        ];
	// 	    })
	// 	    ->sortByDesc('count')
	// 	    ->values()
	// 	    ->take(5);


	// 	$data = [
	// 		'total_appointments' => $totalAppointments,
	// 		'total_price' => $totalPrice,
	// 		'total_confirmed' => $totalConfirmed,
	// 		'total_canceled' => $totalCanceled,
	// 		'next_7_days_appointments' => $next7DaysAppointments,
	// 		'next_30_days_appointments' => $next30DaysAppointments,
	// 		'todays_appointments' => $todaysAppointments,
	// 		'appointment_activities' => $appointmentActivities,
	// 		'top_services_this_month' => $topServicesThisMonth,
	// 		'top_services_last_month' => $topServicesLastMonth,
	// 	];
		
	// 	return $this->success($data, 'Appointment analytics fetched successfully',200);
	// }


	public function appointmentAnalytics(Request $request)
	{
	    $storeId = $request->input('online_store_id');

	    if (!$storeId) {
	        return $this->error([], 'Online store ID is required.', 422);
	    }

	    $filter = $request->input('filter'); // accepts: next_7_days, next_30_days

	    $today = now()->startOfDay();
	    $next7Days = now()->addDays(7)->endOfDay();
	    $next30Days = now()->addDays(30)->endOfDay();
	    $startOfMonth = now()->startOfMonth();
	    $startOfLastMonth = now()->subMonth()->startOfMonth();
	    $endOfLastMonth = now()->subMonth()->endOfMonth();

	    $appointmentIds = Appointment::where('online_store_id', $storeId)->pluck('id');

	    $appointmentsQuery = Appointment::where('online_store_id', $storeId);

	    $totalAppointments = $appointmentIds->count();
	    $totalConfirmed = Appointment::whereIn('id', $appointmentIds)->where('status', 'confirmed')->count();
	    $totalCanceled = Appointment::whereIn('id', $appointmentIds)->where('status', 'canceled')->count();

	    $totalPrice = Payment::whereIn('appointment_id', $appointmentIds)
	        ->where('status', 'succeeded')
	        ->sum('amount');

	    // Load both but conditionally attach to response
	    $next7DaysAppointments = (clone $appointmentsQuery)
	        ->whereBetween('date', [$today, $next7Days])
	        ->orderBy('date')
	        ->get();

	    $next30DaysAppointments = (clone $appointmentsQuery)
	        ->whereBetween('date', [$today, $next30Days])
	        ->orderBy('date')
	        ->get();

	    $todaysAppointments = (clone $appointmentsQuery)
	        ->whereDate('date', now()->toDateString())
	        ->orderBy('time')
	        ->get();

	    $appointmentActivities = (clone $appointmentsQuery)
	        ->with(['user', 'storeServices'])
	        ->orderBy('created_at', 'desc')
	        ->take(20)
	        ->get();

	    $topServicesThisMonth = Appointment::where('online_store_id', $storeId)
	        ->whereBetween('date', [$startOfMonth, now()])
	        ->with('storeServices.service')
	        ->get()
	        ->flatMap->storeServices
	        ->groupBy('id')
	        ->map(function ($services) {
	            return [
	                'service_name' => optional($services->first()->service)->service_name ?? 'Unknown',
	                'count' => $services->count(),
	            ];
	        })
	        ->sortByDesc('count')
	        ->values()
	        ->take(5);

	    $topServicesLastMonth = Appointment::where('online_store_id', $storeId)
	        ->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
	        ->with('storeServices.service')
	        ->get()
	        ->flatMap->storeServices
	        ->groupBy('id')
	        ->map(function ($services) {
	            return [
	                'service_name' => optional($services->first()->service)->service_name ?? 'Unknown',
	                'count' => $services->count(),
	            ];
	        })
	        ->sortByDesc('count')
	        ->values()
	        ->take(5);

	    // Build response dynamically based on filter
	    $data = [
	        'total_appointments' => $totalAppointments,
	        'total_price' => $totalPrice,
	        'total_confirmed' => $totalConfirmed,
	        'total_canceled' => $totalCanceled,
	        'todays_appointments' => $todaysAppointments,
	        'appointment_activities' => $appointmentActivities,
	        'top_services_this_month' => $topServicesThisMonth,
	        'top_services_last_month' => $topServicesLastMonth,
	    ];

	    if ($filter === 'next_7_days') {
	        $data['next_7_days_appointments'] = $next7DaysAppointments;
	    } elseif ($filter === 'next_30_days') {
	        $data['next_30_days_appointments'] = $next30DaysAppointments;
	    } else {
	        $data['next_7_days_appointments'] = $next7DaysAppointments;
	        $data['next_30_days_appointments'] = $next30DaysAppointments;
	    }

	    return $this->success($data, 'Appointment analytics fetched successfully', 200);
	}

	

	public function clientAnalytics(Request $request)
	{
	    $storeId = $request->input('online_store_id');

	    if (!$storeId) {
	        return $this->error([], 'Online store ID is required.', 422);
	    }

	    $clients = User::whereHas('appointments', function ($query) use ($storeId) {
	            $query->where('online_store_id', $storeId);
	        })
	        ->with(['appointments' => function ($query) use ($storeId) {
	            $query->where('online_store_id', $storeId)
	                  ->with('payments');
	        }])
	        ->get()
	        ->map(function ($user) {
	            $appointments = $user->appointments;

	            $totalAppointments = $appointments->count();
	            $totalConfirmed = $appointments->where('status', 'confirmed')->count();
	            $totalCanceled = $appointments->where('status', 'canceled')->count();

	            $totalSpent = $appointments->flatMap->payments
	                ->where('status', 'succeeded')
	                ->sum('amount');

	            return [
	                'client_id' => $user->id,
	                'name' => $user->first_name.' '.$user->last_name,
	                'email' => $user->email,
	                'phone' => $user->number,
	                'total_appointments' => $totalAppointments,
	                'total_confirmed' => $totalConfirmed,
	                'total_canceled' => $totalCanceled,
	                'total_spent' => $totalSpent,
	            ];
	        })
	        ->sortByDesc('total_spent')
	        ->values();

	    return $this->success($clients, 'Client analytics fetched successfully', 200);
	}



}
