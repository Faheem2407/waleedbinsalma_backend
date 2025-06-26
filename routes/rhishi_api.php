<?php

use App\Http\Controllers\Api\ConnectAccountController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['jwt.verify'])->group(function () {
    Route::controller(ConnectAccountController::class)->prefix('stripe/account')->group(function () {
        Route::post('/connect', 'connectAccount');
    });
});

Route::controller(ConnectAccountController::class)->prefix('instructor')->group(function () {
    Route::get('/connect/success', 'connectSuccess')->name('connect.success');
    Route::get('/connect/cancel', 'connectCancel')->name('connect.cancel');
});
