<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentCreateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OnlineStoreController;
use App\Http\Controllers\Api\BookmarkController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CustomerDashboardController;

Route::controller(OnlineStoreController::class)->prefix('online-store')->group(function () {
    Route::post('/register', 'createOrUpdate');
    Route::get('/details/{business_profile_id}', 'getOnlineStoreIdByBusinessProfile');
    Route::get('/show', 'getRegister');
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

    // Book Appointment
    Route::controller(AppointmentController::class)->prefix('online-store')->group(function () {
        Route::get('/appointments', 'myAppointments');
        Route::post('/appointment/reschedule/{id}', 'rescheduleAppointment');
        Route::post('/appointment/cancel/{id}', 'cancelAppointment');
    });

    Route::controller(CustomerDashboardController::class)->prefix('customer-dashboard')->group(function () {
        Route::get('/profile', 'showProfile');
        Route::post('/update-profile', 'updateProfile');
        Route::post('/add-address', 'addAddress');
        Route::post('/edit-address', 'editAddress');
        Route::post('/delete-address', 'deleteAddress');
        Route::get('/my-favorites', 'myFavorites');
        Route::get('/my-appointments', 'myAppointments');
        Route::get('/my-products', 'myProducts');
    });

    Route::controller(AppointmentCreateController::class)->prefix('online-store')->group(function () {
        Route::post('/appointment/book', 'bookAppointment');
    });
});

Route::controller(AppointmentCreateController::class)->prefix('online-store')->group(function () {
    Route::get('/appointment/book/success', 'bookAppointmentSuccess')->name('appointment.book.success');
    Route::get('/appointment/book/cancel', 'bookAppointmentCancel')->name('appointment.book.cancel');
});
