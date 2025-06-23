<?php

use App\Http\Controllers\Web\Backend\Amenities\AmenitiesController;
use App\Http\Controllers\Web\Backend\Highlights\HighlightsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\Service\ServiceController;
use App\Http\Controllers\Web\Backend\Values\ValuesController;


Route::controller(ServiceController::class)->prefix('service')->name('service.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/status/{id}', 'status')->name('status');
    Route::post('/destroy/{id}', 'destroy')->name('destroy');
});

Route::resource('amenities', AmenitiesController::class);
Route::post('/amenities/{id}/status', [AmenitiesController::class, 'status'])->name('amenities.status');

Route::resource('highlights', HighlightsController::class);
Route::post('/highlights/{id}/status', [HighlightsController::class, 'status'])->name('highlights.status');

Route::resource('values', ValuesController::class);
Route::post('/values/{id}/status', [ValuesController::class, 'status'])->name('values.status');
