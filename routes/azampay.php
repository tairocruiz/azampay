<?php

use Illuminate\Support\Facades\Route;
use Taitech\Azampay\Controllers\AzampayController;

/*
|--------------------------------------------------------------------------
| AzamPay Routes
|--------------------------------------------------------------------------
|
| These routes handle AzamPay payment callbacks and webhooks.
| You can customize these routes as needed for your application.
|
*/

// Webhook endpoint for AzamPay callbacks
Route::post(config('azampay.webhook_route', 'azampay/webhook'), [AzampayController::class, 'webhook'])
    ->name('azampay.webhook');

// Payment checkout endpoint
Route::post('azampay/checkout', [AzampayController::class, 'checkout'])
    ->name('azampay.checkout');

// Payment status check endpoint
Route::get('azampay/status/{reference}', [AzampayController::class, 'status'])
    ->name('azampay.status');
