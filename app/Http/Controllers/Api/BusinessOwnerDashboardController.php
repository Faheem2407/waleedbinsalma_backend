<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OnlineStore;

class BusinessOwnerDashboardController extends Controller
{
	use ApiResponse;

	public function appointmentAnalytics(Request $request)
	{

	    $storeId = $request->input('online_store_id');
	    $filter = $request->input('filter','next_7_days'); // next_7_days | next_30_days | null

	    if (!$storeId) {
	        return $this->error([], 'Online store ID is required.', 422);
	    }

	    $today = now()->startOfDay();
	    $next7Days = now()->addDays(7)->endOfDay();
	    $next30Days = now()->addDays(30)->endOfDay();
	    $startOfMonth = now()->startOfMonth();
	    $startOfLastMonth = now()->subMonth()->startOfMonth();
	    $endOfLastMonth = now()->subMonth()->endOfMonth();
	    $last7Days = now()->subDays(6)->startOfDay(); // For daily sales from last 7 days

	    $appointmentIds = Appointment::where('online_store_id', $storeId)->pluck('id');
	    $appointmentsQuery = Appointment::where('online_store_id', $storeId);

	    // Summary counts
	    $totalAppointments = $appointmentIds->count();
	    $totalConfirmed = Appointment::whereIn('id', $appointmentIds)->where('status', 'confirmed')->count();
	    $totalCanceled = Appointment::whereIn('id', $appointmentIds)->where('status', 'cancelled')->count();

	    $totalPrice = Payment::whereIn('appointment_id', $appointmentIds)
	        ->where('status', 'succeeded')
	        ->sum('amount');

	    // Appointments for next 7 days
	    $next7DaysBase = (clone $appointmentsQuery)->whereBetween('date', [$today, $next7Days]);
	    $next7DaysAppointments = $next7DaysBase->with('storeServices.service')->orderBy('date')->get();

	    $next7DaysConfirmed = (clone $next7DaysBase)->where('status', 'confirmed')->count();
	    $next7DaysCanceled = (clone $next7DaysBase)->where('status', 'cancelled')->count();

	    // Appointments for next 30 days
	    $next30DaysBase = (clone $appointmentsQuery)->whereBetween('date', [$today, $next30Days]);
	    $next30DaysAppointments = $next30DaysBase->with('storeServices.service')->orderBy('date')->get();

	    $next30DaysConfirmed = (clone $next30DaysBase)->where('status', 'confirmed')->count();
	    $next30DaysCanceled = (clone $next30DaysBase)->where('status', 'cancelled')->count();

	    // Today's appointments
	    $todaysAppointments = (clone $appointmentsQuery)
	        ->whereDate('date', now()->toDateString())
	        ->orderBy('time')
	        ->with('storeServices.service')
	        ->get();

	    // Activities
	    $appointmentActivities = (clone $appointmentsQuery)
	        ->with(['user', 'storeServices.service'])
	        ->orderBy('created_at', 'desc')
	        ->take(20)
	        ->get();

	    // Top services this month
	    $topServicesThisMonth = Appointment::where('online_store_id', $storeId)
	        ->whereBetween('date', [$startOfMonth, now()])
	        ->with('storeServices.service')
	        ->get()
	        ->flatMap->storeServices
	        ->groupBy('id')
	        ->map(fn($services) => [
	            'service_name' => optional($services->first()->service)->service_name ?? 'Unknown',
	            'count' => $services->count(),
	        ])
	        ->sortByDesc('count')
	        ->values()
	        ->take(5);

	    // Top services last month
	    $topServicesLastMonth = Appointment::where('online_store_id', $storeId)
	        ->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
	        ->with('storeServices.service')
	        ->get()
	        ->flatMap->storeServices
	        ->groupBy('id')
	        ->map(fn($services) => [
	            'service_name' => optional($services->first()->service)->service_name ?? 'Unknown',
	            'count' => $services->count(),
	        ])
	        ->sortByDesc('count')
	        ->values()
	        ->take(5);

	    // Daily sales for last 7 days
	    $recentSales = Payment::whereIn('appointment_id', $appointmentIds)
	        ->where('status', 'succeeded')
	        ->whereBetween('created_at', [$last7Days, now()])
	        ->get()
	        ->groupBy(fn($payment) => \Carbon\Carbon::parse($payment->created_at)->format('Y-m-d'))
	        ->map(fn($payments) => $payments->sum('amount'))
	        ->toArray();

	    // Fill in missing dates with 0
	    $recentSalesLast7Days = [];
	    for ($i = 0; $i < 7; $i++) {
	        $date = now()->subDays(6 - $i)->format('Y-m-d');
	        $recentSalesLast7Days[$date] = $recentSales[$date] ?? 0;
	    }

	    // Final Data
	    $data = [
	        'total_appointments' => $totalAppointments,
	        'total_price' => $totalPrice,
	        'total_confirmed' => $totalConfirmed,
	        'total_canceled' => $totalCanceled,
	        'todays_appointments' => $todaysAppointments,
	        'appointment_activities' => $appointmentActivities,
	        'top_services_this_month' => $topServicesThisMonth,
	        'top_services_last_month' => $topServicesLastMonth,
	        'recent_sales_last_7_days' => $recentSalesLast7Days,
	    ];

	    // Conditionally include next 7 or 30 day appointments
	    if ($filter === 'next_7_days') {
	        $data['next_days_appointments'] = [
	            'appointments' => $next7DaysAppointments,
	            'total_confirmed' => $next7DaysConfirmed,
	            'total_canceled' => $next7DaysCanceled,
	        ];
	    } elseif ($filter === 'next_30_days') {
	        $data['next_days_appointments'] = [
	            'appointments' => $next30DaysAppointments,
	            'total_confirmed' => $next30DaysConfirmed,
	            'total_canceled' => $next30DaysCanceled,
	        ];
	    } else {
	        $data['next_7_days_appointments'] = [
	            'appointments' => $next7DaysAppointments,
	            'total_confirmed' => $next7DaysConfirmed,
	            'total_canceled' => $next7DaysCanceled,
	        ];
	        $data['next_30_days_appointments'] = [
	            'appointments' => $next30DaysAppointments,
	            'total_confirmed' => $next30DaysConfirmed,
	            'total_canceled' => $next30DaysCanceled,
	        ];
	    }

	    return $this->success($data, 'Appointment analytics fetched successfully', 200);
	}



	public function productSalesAnalytics(Request $request)
	{
	    $storeId = $request->input('online_store_id');
	    $filter = $request->input('filter', 'daily'); // Options: daily, weekly, yearly

	    if (!$storeId) {
	        return $this->error([], 'Online store ID is required.', 422);
	    }

	    // Define time window based on filter
	    $now = now();
	    $startDate = match ($filter) {
	        'weekly' => $now->copy()->startOfWeek(),
	        'yearly' => $now->copy()->startOfYear(),
	        default => $now->copy()->startOfDay()
	    };

	    // Get all relevant product orders
	    $orders = Order::with(['items.product'])
	        ->where('online_store_id', $storeId)
	        ->where('payment_status', 'succeeded')
	        ->whereBetween('created_at', [$startDate, $now])
	        ->get();

	    $sales = [];
	    $grandTotal = 0;

	    foreach ($orders as $order) {
	        foreach ($order->items as $item) {
	            $timestamp = \Carbon\Carbon::parse($item->created_at);

	            $groupKey = match ($filter) {
	                'weekly' => $timestamp->format('Y-m-d'), // Group by day in the week
	                'yearly' => $timestamp->format('F'),      // Group by month
	                default => $timestamp->format('Y-m-d'),   // Daily
	            };

	            $productId = $item->product?->id;
	            $productName = $item->product?->name ?? 'Unknown';

	            $amount = $item->quantity * $item->price;
	            $grandTotal += $amount;

	            if (!isset($sales[$groupKey])) {
	                $sales[$groupKey] = [];
	            }

	            if (!isset($sales[$groupKey][$productId])) {
	                $sales[$groupKey][$productId] = [
	                    'product_id' => $productId,
	                    'product_name' => $productName,
	                    'total_quantity' => 0,
	                    'total_revenue' => 0,
	                ];
	            }

	            $sales[$groupKey][$productId]['total_quantity'] += $item->quantity;
	            $sales[$groupKey][$productId]['total_revenue'] += $amount;
	        }
	    }

	    // Prepare response
	    $groupedSales = collect($sales)->map(function ($products, $period) {
	        return [
	            'period' => $period,
	            'products' => array_values($products),
	        ];
	    })->values();

	    return $this->success([
	        'filter' => $filter,
	        'store_id' => $storeId,
	        'total_sales_amount' => $grandTotal,
	        'sales' => $groupedSales,
	    ], 'Product sales analytics fetched successfully.', 200);
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
	            $totalCanceled  = $appointments->where('status', 'cancelled')->count();

	            $totalSpent = $appointments->flatMap->payments
	                ->where('status', 'succeeded')
	                ->sum('amount');

	            $firstAppointment = $appointments->sortBy('created_at')->first();
	            $createdAt = $firstAppointment ? $firstAppointment->created_at : null;

	            return [
	                'client_id'          => $user->id,
	                'name'               => $user->first_name . ' ' . $user->last_name,
	                'email'              => $user->email,
	                'phone'              => $user->number,
	                'created_at'         => $createdAt,
	                'total_appointments' => $totalAppointments,
	                'total_confirmed'    => $totalConfirmed,
	                'total_canceled'     => $totalCanceled,
	                'total_spent'        => $totalSpent,
	            ];
	        })
	        ->sortByDesc('total_spent')
	        ->values();

	    return $this->success($clients, 'Client analytics fetched successfully', 200);
	}


}
