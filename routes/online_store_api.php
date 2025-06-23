<?php

use App\Http\Controllers\Api\AppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OnlineStoreController;
use App\Http\Controllers\Api\BookmarkController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CustomerDashboardController;

Route::prefix('online-store')->group(function () {
    Route::post('/register', [OnlineStoreController::class, 'register']);
    Route::post('/update/{id}',[OnlineStoreController::class,'update']);
    Route::get('/show', [OnlineStoreController::class, 'getRegister']);
});


Route::controller(OnlineStoreController::class)->prefix('online-store')->group(function () {
    Route::get('/show-all', 'showAllOnlineStores');
    Route::get('/show-details/{id}', 'showOnlineStoreDetails');
    Route::get('/product/{id}', 'viewProduct');
});


Route::group(['middleware' => ['jwt.verify']], function () {
    Route::controller(BookmarkController::class)->prefix('online-store')->group(function () {
        Route::post('/bookmark/add', 'add');
        Route::post('/bookmark/remove', 'remove');
    });
    // book appointment
    Route::controller(AppointmentController::class)->prefix('online-store')->group(function () {
        Route::post('/appointment/book', 'bookAppointment');
        Route::get('/appointments', 'myAppointments');
        Route::post('/appointment/reschedule/{id}', 'rescheduleAppointment');
        Route::post('/appointment/cancel/{id}', 'cancelAppointment');
    });


    Route::controller(CartController::class)->prefix('product-cart')->group(function () {
        Route::post('/add', 'add');
        Route::delete('/remove', 'remove');
        Route::get('/view', 'view');
    });

    Route::controller(CartController::class)->prefix('cart')->group(function () {
        Route::post('create-payment-intent', 'createCartPaymentIntent');
        Route::post('finalize-order', 'finalizeCartOrder');
    });


    Route::controller(CustomerDashboardController::class)->prefix('customer-dashboard')->group(function () {
        Route::get('/profile', 'showProfile');
        Route::post('/update-profile','updateProfile');
        Route::post('/add-address', 'addAddress');
        Route::post('/edit-address', 'editAddress');
        Route::post('/delete-address', 'deleteAddress');
        Route::get('/my-favorites', 'myFavorites');
        Route::get('/my-appointments','myAppointments');
        Route::get('/my-products', 'myProducts');
    });

});