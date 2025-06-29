<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusinessOwnerDashboardController;

Route::controller(BusinessOwnerDashboardController::class)->prefix('/business-owner')->group(function () {
    Route::get('/appointments/analytics', 'appointmentAnalytics');
    // Route::post('/daily-sales','productSalesAnalytics');
    Route::get('/clients/analytics','clientAnalytics');
    Route::get('/appointments','appointmentList');
    Route::get('/products','productList');
    Route::get('/daily-sales', 'dailySales');
    Route::get('/sales', 'sales');

});


