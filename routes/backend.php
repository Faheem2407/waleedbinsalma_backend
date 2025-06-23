<?php

use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\Service\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\CMS\BusinessHomeController;
use App\Http\Controllers\Web\Backend\CMS\BusinessPricingController;
use App\Http\Controllers\Web\Backend\CMS\BusinessHelpController;
use App\Http\Controllers\Web\Backend\BlogController;
use App\Http\Controllers\Web\Backend\CMS\BlogController as CMSBlogController;
use App\Http\Controllers\Web\Backend\BlogCategoryController;
use App\Http\Controllers\Web\Backend\ClientReviewController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

//FAQ Routes
Route::controller(FaqController::class)->group(function () {
    Route::get('/faqs', 'index')->name('admin.faqs.index');
    Route::get('/faqs/create', 'create')->name('admin.faqs.create');
    Route::post('/faqs/store', 'store')->name('admin.faqs.store');
    Route::get('/faqs/edit/{id}', 'edit')->name('admin.faqs.edit');
    Route::post('/faqs/update/{id}', 'update')->name('admin.faqs.update');
    Route::post('/faqs/status/{id}', 'status')->name('admin.faqs.status');
    Route::post('/faqs/destroy/{id}', 'destroy')->name('admin.faqs.destroy');
});

//CMS Routes
Route::controller(BusinessHomeController::class)->prefix('admin/cms')->name('admin.cms.businessHome.banner.')->group(function () {
    Route::get('business-home/banner', 'businessHomeBannerIndex')->name('index');
    Route::post('business-home/banner/update', 'businessHomeBannerUpdate')->name('update');
});

// home stats
Route::controller(BusinessHomeController::class)->prefix('admin/cms')->name('admin.cms.businessHome.stats.')->group(function () {
    Route::get('business-home/stats', 'businessHomeStatsIndex')->name('index');
    Route::post('business-home/stats/update', 'businessHomeStatsUpdate')->name('update');
});

Route::controller(BusinessHomeController::class)->prefix('admin/cms/business-grow')->name('businessHome.grow.')->group(function () {
    Route::get('/', 'businessGrowIndex')->name('index');
    Route::get('create', 'businessGrowCreate')->name('create');
    Route::post('store', 'businessGrowStore')->name('store');
    Route::get('edit/{id}', 'businessGrowEdit')->name('edit');
    Route::post('update/{id}', 'businessGrowUpdate')->name('update');
    Route::get('status/{id}', 'businessGrowStatus')->name('status');
    Route::delete('destroy/{id}', 'businessGrowDestroy')->name('destroy');
});

// Business Grow section title and subtitle index and update
Route::controller(BusinessHomeController::class)->prefix('admin/cms/business-grow/section-title')->name('businessHome.grow.sectionTitle.')->group(function () {
    Route::get('/', 'businessGrowSectionTitleIndex')->name('index');
    Route::post('update', 'businessGrowSectionTitleUpdate')->name('update');
});

// business home stay connected section
Route::controller(BusinessHomeController::class)->prefix('admin/cms/business-stay-connection')->name('businessHome.stayConnection.')->group(function () {
    Route::get('/', 'businessStayConnectionIndex')->name('index');
    Route::get('create', 'businessStayConnectionCreate')->name('create');
    Route::post('store', 'businessStayConnectionStore')->name('store');
    Route::get('edit/{id}', 'businessStayConnectionEdit')->name('edit');
    Route::post('update/{id}', 'businessStayConnectionUpdate')->name('update');
    Route::get('status/{id}', 'businessStayConnectionStatus')->name('status');
    Route::delete('destroy/{id}', 'businessStayConnectionDestroy')->name('destroy');
});

Route::controller(BusinessHomeController::class)->prefix('admin/cms')->name('admin.cms.businessHome.getStarted.')->group(function () {
    Route::get('business-home/get-started', 'businessHomeGetStartedIndex')->name('index');
    Route::post('business-home/get-started/update', 'businessHomeGetStartedUpdate')->name('update');
});

Route::controller(BusinessHomeController::class)->prefix('admin/cms')->name('admin.cms.businessHome.interested.')->group(function () {
    Route::get('business-home/interested', 'businessHomeInterestedIndex')->name('index');
    Route::post('business-home/interested/update', 'businessHomeInterestedUpdate')->name('update');
});

Route::controller(BusinessHomeController::class)->prefix('admin/cms')->name('admin.cms.businessHome.whatOurClientSay.')->group(function () {
    Route::get('business-home/what-our-client-say', 'businessHomeWhatOurClientSayIndex')->name('index');
    Route::post('business-home/what-our-client-say/update', 'businessHomeWhatOurClientSayUpdate')->name('update');
});

// Business Pricing page
Route::controller(BusinessPricingController::class)->prefix('admin/cms')->name('businessPricing.banner.')->group(function () {
    Route::get('business-pricing/banner', 'businessPricingBannerIndex')->name('index');
    Route::post('business-pricing/banner/update', 'businessPricingBannerUpdate')->name('update');
});

// Business Pricing section title
Route::controller(BusinessPricingController::class)->prefix('admin/cms/business-pricing/section-title')->name('businessPricing.sectionTitle.')->group(function () {
    Route::get('/', 'businessPricingSectionTitleIndex')->name('index');
    Route::post('update', 'businessPricingSectionTitleUpdate')->name('update');
});
// Business Pricing section description
Route::controller(BusinessPricingController::class)->prefix('admin/cms/business-pricing/section-description')->name('businessPricing.sectionDescription.')->group(function () {
    Route::get('/', 'businessPricingSectionDescriptionIndex')->name('index');
    Route::post('update', 'businessPricingSectionDescriptionUpdate')->name('update');
});


Route::controller(BusinessPricingController::class)->prefix('admin/cms/business-pricing/lists')->name('businessPricing.description.')->group(function () {
    Route::get('/', 'businessPricingDescriptionIndex')->name('index');
    Route::get('create', 'businessPricingDescriptionCreate')->name('create');
    Route::post('store', 'businessPricingDescriptionStore')->name('store');
    Route::get('edit/{id}', 'businessPricingDescriptionEdit')->name('edit');
    Route::post('update/{id}', 'businessPricingDescriptionUpdate')->name('update');
    Route::get('status/{id}', 'businessPricingDescriptionStatus')->name('status');
    Route::delete('destroy/{id}', 'businessPricingDescriptionDestroy')->name('destroy');
});

// Business Pricing FAQ
Route::controller(BusinessPricingController::class)->prefix('admin/cms/business-pricing/faq')->name('businessPricing.faq.')->group(function () {
    Route::get('/', 'businessPricingFaqIndex')->name('index');
    Route::get('create', 'businessPricingFaqCreate')->name('create');
    Route::post('store', 'businessPricingFaqStore')->name('store');
    Route::get('edit/{id}', 'businessPricingFaqEdit')->name('edit');
    Route::post('update/{id}', 'businessPricingFaqUpdate')->name('update');
    Route::get('status/{id}', 'businessPricingFaqStatus')->name('status');
    Route::delete('destroy/{id}', 'businessPricingFaqDestroy')->name('destroy');
});



// Business Help page
Route::controller(BusinessHelpController::class)->prefix('admin/cms')->name('admin.cms.businessHelp.banner.')->group(function () {
    Route::get('business-help/banner', 'businessHelpBannerIndex')->name('index');
    Route::post('business-help/banner/update', 'businessHelpBannerUpdate')->name('update');
});

Route::controller(BusinessHelpController::class)->prefix('admin/cms')->name('admin.cms.businessHelp.popularArticleBanner.')->group(function () {
    Route::get('business-help/popular-article-banner', 'businessHelpPopularArticleBannerIndex')->name('index');
    Route::post('business-help/popular-article-banner/update', 'businessHelpPopularArticleBannerUpdate')->name('update');
});

// Business Help popular articles
Route::controller(BusinessHelpController::class)->prefix('admin/cms/business-help/popular-articles')->name('businessHelp.popularArticles.')->group(function () {
    Route::get('/', 'businessPopularArticlesIndex')->name('index');
    Route::get('create', 'businessPopularArticlesCreate')->name('create');
    Route::post('store', 'businessPopularArticlesStore')->name('store');
    Route::get('edit/{id}', 'businessPopularArticlesEdit')->name('edit');
    Route::post('update/{id}', 'businessPopularArticlesUpdate')->name('update');
    Route::get('status/{id}', 'businessPopularArticlesStatus')->name('status');
    Route::delete('destroy/{id}', 'businessPopularArticlesDestroy')->name('destroy');
});

Route::controller(BusinessHelpController::class)->prefix('admin/cms')->name('businessHelp.knowledgeBaseBanner.')->group(function () {
    Route::get('business-help/knowledge-base-banner', 'businessHelpKnowledgeBaseBannerIndex')->name('index');
    Route::post('business-help/knowledge-base-banner/update', 'businessHelpKnowledgeBaseBannerUpdate')->name('update');
});

// Business Help knowledge base
Route::controller(BusinessHelpController::class)->prefix('admin/cms/business-help/knowledge-base')->name('businessHelp.knowledgeBase.')->group(function () {
    Route::get('/', 'businessKnowledgeBaseIndex')->name('index');
    Route::get('create', 'businessKnowledgeBaseCreate')->name('create');
    Route::post('store', 'businessKnowledgeBaseStore')->name('store');
    Route::get('edit/{id}', 'businessKnowledgeBaseEdit')->name('edit');
    Route::post('update/{id}', 'businessKnowledgeBaseUpdate')->name('update');
    Route::get('status/{id}', 'businessKnowledgeBaseStatus')->name('status');
    Route::delete('destroy/{id}', 'businessKnowledgeBaseDestroy')->name('destroy');
});


Route::controller(BusinessHelpController::class)->prefix('admin/cms')->name('businessHelp.help.')->group(function () {
    Route::get('business-help/help', 'businessHelpIndex')->name('index');
    Route::post('business-help/help/update', 'businessHelpUpdate')->name('update');
});

Route::controller(CMSBlogController::class)->prefix('admin/cms')->name('cms.blog.banner.')->group(function () {
    Route::get('blog/banner', 'blogBannerIndex')->name('index');
    Route::post('blog/banner/update', 'blogBannerUpdate')->name('update');
});

Route::controller(CMSBlogController::class)->prefix('admin/cms')->name('cms.blog.footer.')->group(function () {
    Route::get('blog/footer', 'blogFooterIndex')->name('index');
    Route::post('blog/footer/update', 'blogFooterUpdate')->name('update');
});


// Blog Category
Route::controller(BlogCategoryController::class)->prefix('admin/cms')->name('blogCategory.')->group(function () {
    Route::get('blog-category', 'index')->name('index');
    Route::get('blog-category/create', 'create')->name('create');
    Route::post('blog-category/store', 'store')->name('store');
    Route::get('blog-category/edit/{id}', 'edit')->name('edit');
    Route::post('blog-category/update/{id}', 'update')->name('update');
    Route::get('blog-category/status/{id}', 'status')->name('status');
    Route::delete('blog-category/destroy/{id}', 'destroy')->name('destroy');
});


// blogs
Route::controller(BlogController::class)->prefix('admin/')->name('blog.')->group(function () {
    Route::get('blog', 'index')->name('index');
    Route::get('blog/create', 'create')->name('create');
    Route::post('blog/store', 'store')->name('store');
    Route::get('blog/edit/{id}', 'edit')->name('edit');
    Route::post('blog/update/{id}', 'update')->name('update');
    Route::get('blog/status/{id}', 'status')->name('status');
    Route::delete('blog/destroy/{id}', 'destroy')->name('destroy');
});

// client_reviews
Route::controller(ClientReviewController::class)->prefix('admin/')->name('client_review.')->group(function () {
    Route::get('client_review', 'index')->name('index');
    Route::get('client_review/create', 'create')->name('create');
    Route::post('client_review/store', 'store')->name('store');
    Route::get('client_review/edit/{id}', 'edit')->name('edit');
    Route::post('client_review/update/{id}', 'update')->name('update');
    Route::get('client_review/status/{id}', 'status')->name('status');
    Route::delete('client_review/destroy/{id}', 'destroy')->name('destroy');
});
