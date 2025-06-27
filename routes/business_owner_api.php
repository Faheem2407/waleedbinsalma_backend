<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusinessOwnerDashboardController;
use App\Http\Controllers\Api\Review\AppointmentReviewController;

Route::controller(BusinessOwnerDashboardController::class)->prefix('/business-owner')->group(function () {
    Route::get('/appointments/analytics', 'appointmentAnalytics');
    Route::post('/daily-sales','productSalesAnalytics');
    Route::get('/clients/analytics','clientAnalytics');
});


Route::controller(AppointmentReviewController::class)->prefix('appointment/review')->group(function () {
    Route::post('/submit','submitReview');
});