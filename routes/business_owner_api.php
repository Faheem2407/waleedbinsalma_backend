<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusinessOwnerDashboardController;
use App\Http\Controllers\Api\AppointmentController;

Route::controller(BusinessOwnerDashboardController::class)->prefix('/business-owner')->group(function () {
    Route::get('/appointments/analytics', 'appointmentAnalytics');
    Route::get('/clients/analytics','clientAnalytics');
    Route::get('/appointments','appointmentList');
    Route::get('/products','productList');
    Route::get('/daily-sales', 'dailySales');
    Route::get('/sales', 'sales');
});


Route::controller(AppointmentController::class)->prefix('appointments')->group(function (){
    Route::get('/this-week-total','totalAppointmentsThisWeek');
});


