<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Appointment;
use App\Models\Payment;

class BusinessOwnerDashboardController extends Controller
{
	use ApiResponse;
	public function appointmentAnalytics(Request $request)
	{
		$storeId = $request->input('online_store_id');

		if (!$storeId) {
			return $this->error([], 'Online store ID is required.', 422);
		}

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

		// Top services this month
		$topServicesThisMonth = Appointment::where('online_store_id', $storeId)
			->whereBetween('date', [$startOfMonth, now()])
			->with('storeServices')
			->get()
			->flatMap->storeServices
			->groupBy('id')
			->map(function ($services) {
				return [
					'service_name' => $services->first()->name,
					'count' => $services->count(),
				];
			})
			->sortByDesc('count')
			->values()
			->take(5);

		// Top services last month
		$topServicesLastMonth = Appointment::where('online_store_id', $storeId)
			->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
			->with('storeServices')
			->get()
			->flatMap->storeServices
			->groupBy('id')
			->map(function ($services) {
				return [
					'service_name' => $services->first()->name,
					'count' => $services->count(),
				];
			})
			->sortByDesc('count')
			->values()
			->take(5);

		return $this->success([
			'total_appointments' => $totalAppointments,
			'total_price' => $totalPrice,
			'total_confirmed' => $totalConfirmed,
			'total_canceled' => $totalCanceled,
			'next_7_days_appointments' => $next7DaysAppointments,
			'next_30_days_appointments' => $next30DaysAppointments,
			'todays_appointments' => $todaysAppointments,
			'appointment_activities' => $appointmentActivities,
			'top_services_this_month' => $topServicesThisMonth,
			'top_services_last_month' => $topServicesLastMonth,
		], 'Appointment analytics fetched successfully');
	}


}
