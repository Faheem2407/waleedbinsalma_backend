<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Catalog\CatalogServiceCategoryController;
use App\Http\Controllers\Api\Catalog\CatalogServiceController;
use App\Http\Controllers\Api\Catalog\TeamController;
use App\Http\Controllers\Api\Catalog\ProductController;
use App\Http\Controllers\Api\Catalog\ProductBrandController;
use App\Http\Controllers\Api\Catalog\ProductCategoryController;

Route::controller(CatalogServiceCategoryController::class)->group(function () {
    Route::get('/catalog-service-categories', 'catalogServiceCategoriesCount');
    Route::post('/catalog/add-service-categories', 'addCategory');
    Route::post('/catalog/edit-service-categories/{id}', 'editCategory');
    Route::get('/catalog/show-service-categories/{id}', 'showCategory');
    Route::delete('/catalog/delete-service-categories/{id}','deleteCategory');
});

Route::controller(CatalogServiceController::class)->prefix('catalog-services')->group(function () {
    Route::get('/',  'index');
    Route::post('/store',  'store');
    Route::post('/update/{id}','update');
    Route::get('/show/{id}',  'show');
    Route::delete('/destroy/{id}',  'destroy');
    Route::post('/{id}/update-team-members', 'updateTeamMembers');
    Route::get('/search',  'search');
    Route::get('/filter', 'filter');
});

Route::controller(TeamController::class)->prefix('teams')->group(function () {
    Route::post('/search', 'search');
    Route::get('/',  'index');
    Route::post('/',  'store');
    Route::get('{id}', 'show');
    Route::post('{id}', 'update');
    Route::delete('{id}', 'destroy');
});


// Route::controller(CatalogServiceSettingController::class)->group(function () {
//     Route::post('/catalog-services/{id}/settings', 'storeOrUpdate');
// });



Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::post('{id}', 'update');
    Route::delete('{id}', 'destroy');
});



Route::controller(ProductCategoryController::class)->prefix('product-categories')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::post('{id}', 'update');
    Route::delete('{id}', 'destroy');
});


Route::controller(ProductBrandController::class)->prefix('product-brands')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('{id}', 'show');
    Route::post('{id}', 'update');
    Route::delete('{id}', 'destroy');
});
