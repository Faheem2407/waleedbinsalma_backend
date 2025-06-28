<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\BusinessMiddleware;
use App\Http\Middleware\GuestMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JWTMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/shahid_api.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->group(base_path('routes/backend.php'));
            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/shahid_backend.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->group(base_path('routes/admin_setting.php'));

            Route::middleware(['web'])
                ->group(base_path('routes/frontend.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/cms_api.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/catalog_api.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/online_store_api.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/business_owner_api.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/rhishi_api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.verify' => JWTMiddleware::class,
            'admin' => Admin::class,
            'business' => BusinessMiddleware::class,
            'guest' => GuestMiddleware::class
        ]);
    })
    ->withBroadcasting(__DIR__ . '/../routes/channels.php', ['prefix' => 'api', 'middleware' => ['jwt.verify']],)
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
