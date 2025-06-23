<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BusinessOwnerDashboardController;


Route::controller(BusinessOwnerDashboardController::class)->prefix('/business-owner')->group(function () {
    Route::get('/appointments/analytics', 'appointmentAnalytics');
});