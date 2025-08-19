<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AzamPay Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for AzamPay payment integration.
    | You can override these values in your .env file.
    |
    */

    // Application credentials
    'app_name' => env('AZAMPAY_APP_NAME', ''),
    'client_id' => env('AZAMPAY_CLIENT_ID', ''),
    'secret' => env('AZAMPAY_CLIENT_SECRET', ''),
    
    // Environment settings
    'env' => env('AZAMPAY_ENV', 'SANDBOX'),
    'default_service' => env('AZAMPAY_SERVICE', 'MNO'),
    
    // HTTP settings
    'timeout' => env('AZAMPAY_TIMEOUT', 30),
    'keep_alive' => env('AZAMPAY_KEEP_ALIVE', true),
    
    // Webhook settings
    'webhook_secret' => env('AZAMPAY_WEBHOOK_SECRET', ''),
    'webhook_route' => env('AZAMPAY_WEBHOOK_ROUTE', 'azampay/webhook'),
    
    // Default currency
    'default_currency' => env('AZAMPAY_CURRENCY', 'TZS'),
    
    // Logging
    'enable_logging' => env('AZAMPAY_ENABLE_LOGGING', true),
    'log_channel' => env('AZAMPAY_LOG_CHANNEL', 'azampay'),
    
    // Activity Log Settings
    'activity_log_enabled' => env('AZAMPAY_ACTIVITY_LOG_ENABLED', true),
    'activity_log_name' => env('AZAMPAY_ACTIVITY_LOG_NAME', 'azampay'),
    'log_payment_events' => env('AZAMPAY_LOG_PAYMENT_EVENTS', true),
    'log_webhook_events' => env('AZAMPAY_LOG_WEBHOOK_EVENTS', true),
    'log_error_events' => env('AZAMPAY_LOG_ERROR_EVENTS', true),
];
