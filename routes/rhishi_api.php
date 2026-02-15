<?php

use App\Http\Controllers\Api\ConnectAccountController;
use App\Http\Controllers\Api\ProductPurchaseController;
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
        // Route::get('/onboard-vendor', 'onboardVendor');
    });

    Route::controller(ConnectAccountController::class)->group(function () {
        Route::get('/onboard-vendor', 'onboardVendor');
    });

    Route::controller(ProductPurchaseController::class)->group(function () {
        Route::post('/product/purchase', 'productPurchase');
    });
});

Route::controller(ConnectAccountController::class)->prefix('instructor')->group(function () {
    Route::get('/connect/success', 'connectSuccess')->name('connect.success');
    Route::get('/connect/cancel', 'connectCancel')->name('connect.cancel');
});

Route::controller(ProductPurchaseController::class)->group(function () {
    Route::get('/product/purchase/checkout-success', 'checkoutSuccess')->name('checkout.success');
    Route::get('/product/purchase/checkout-cancel', 'checkoutCancel')->name('checkout.cancel');
});
