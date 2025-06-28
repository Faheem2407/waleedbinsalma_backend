<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CMS\BusinessHomeController;
use App\Http\Controllers\Api\CMS\BusinessPricingController;
use App\Http\Controllers\Api\CMS\BusinessHelpController;
use App\Http\Controllers\Api\CMS\BlogController;
use App\Http\Controllers\Api\CMS\HomeWithoutSignUpCMSController;

Route::controller(BusinessHomeController::class)->prefix('cms/')->group(function () {
    Route::get('business-home', 'businessHome');
});

Route::controller(BusinessPricingController::class)->prefix('cms/')->group(function () {
    Route::get('business-pricing', 'businessPricing');
    Route::get('faq/search', 'searchFaq');
});

Route::controller(BusinessHelpController::class)->prefix('cms/')->group(function () {
    Route::get('business-help', 'businessHelp');
    Route::get('knowledge-base/{id}',  'knowledgeBaseDetails');
    Route::get('/knowledge-base/item/search', 'searchKnowledgeBase');
    Route::get('popular-article/{id}','popularArticleDetails');
});

Route::controller(BlogController::class)->prefix('cms/')->group(function () {
    Route::get('blog', 'blog');
    Route::get('/blog/{slug}',  'blogDetails');
});

Route::controller(HomeWithoutSignUpCMSController::class)->prefix('cms/')->group(function (){
    Route::get('/business-home-cms-without-signup','businessHomeWithoutSignUp');
});