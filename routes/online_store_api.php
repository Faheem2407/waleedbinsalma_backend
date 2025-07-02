<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AppointmentCreateController;
use App\Http\Controllers\Api\OnlineStoreController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CustomerDashboardController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SubscriptionController;

Route::controller(OnlineStoreController::class)->prefix('online-store')->group(function () {
    Route::post('/register', 'createOrUpdate');
    Route::get('/details/{business_profile_id}', 'getOnlineStoreIdByBusinessProfile');
    Route::get('/show', 'getRegister');
    Route::get('/show-all', 'showAllOnlineStores');
    Route::get('/product/{id}', 'viewProduct');
    Route::get('/trending',  'showTrendingStores');
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/recently-viewed', 'recentlyViewedStores');
        Route::get('/show-details/{id}', 'showOnlineStoreDetails');
    });
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

    Route::controller(OnlineStoreController::class)->prefix('online-store')->group(function () {
        Route::get('/my-bookings', 'myBookingStores');
    });
});

Route::controller(AppointmentCreateController::class)->prefix('online-store')->group(function () {
    Route::get('/appointment/book/success', 'bookAppointmentSuccess')->name('appointment.book.success');
    Route::get('/appointment/book/cancel', 'bookAppointmentCancel')->name('appointment.book.cancel');
});


Route::group(['middleware' => ['jwt.verify']], function () {
    Route::controller(PaymentController::class)->group(function () {
        Route::post('/ad/checkout', 'checkout')->name('checkout');
    });
    Route::controller(SubscriptionController::class)->group(function () {
        Route::post('online-store/subscription/purchase', 'purchase')->name('subscription.purchase');
        Route::post('online-store/subscription/renew', 'renew')->name('subscription.renew');
    });
});

Route::controller(PaymentController::class)->group(function () {
    Route::get('/ad/checkout-success', 'checkoutSuccess')->name('checkout.success');
    Route::get('/ad/checkout-cancel', 'checkoutCancel')->name('checkout.cancel');
});


Route::controller(SubscriptionController::class)->group(function () {
    Route::get('online-store/subscription/success', 'handleSuccess')->name('subscription.success');
    Route::get('online-store/subscription/cancel', 'handleCancel')->name('subscription.cancel');
    Route::get('online-store/subscription/renew/success', 'handleRenewSuccess')->name('subscription.renew.success');
});
