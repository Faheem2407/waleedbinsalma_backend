<?php

use App\Http\Controllers\Api\Amenities\AmenitiesController;
use App\Http\Controllers\Api\Highlight\HighlightsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Services\ServiceController;
use App\Http\Controllers\Api\values\ValuesController;

Route::get("/service_types",[ServiceController::class,'index']);
Route::get('/amenities',[AmenitiesController::class,'index']);
Route::get('/highlights',[HighlightsController::class,'index']);
Route::get('/values',[ValuesController::class,'index']);
