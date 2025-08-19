# Laravel Integration Guide for Azampay

This guide explains how to integrate the Azampay package into your Laravel application.

## Version Compatibility

This package is compatible with:
- **Laravel 9.x** (LTS)
- **Laravel 10.x** (LTS)
- **Laravel 11.x** (Latest)

**Minimum Requirements:**
- PHP ^8.1
- Laravel ^9.0
- GuzzleHTTP ^7.5

## Installation

### 1. Install via Composer

```bash
composer require taitech/azampay
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=azampay-config
```

### 3. Publish Routes (Optional)

```bash
php artisan vendor:publish --tag=azampay-routes
```

### 4. Add Environment Variables

Add the following to your `.env` file:

```env
# AzamPay Configuration
AZAMPAY_APP_NAME=your_app_name
AZAMPAY_CLIENT_ID=your_client_id
AZAMPAY_CLIENT_SECRET=your_client_secret
AZAMPAY_ENV=SANDBOX
AZAMPAY_SERVICE=MNO
AZAMPAY_WEBHOOK_SECRET=your_webhook_secret
AZAMPAY_WEBHOOK_ROUTE=azampay/webhook
AZAMPAY_CURRENCY=TZS
AZAMPAY_ENABLE_LOGGING=true
AZAMPAY_LOG_CHANNEL=azampay

# Activity Log Settings (Optional)
AZAMPAY_ACTIVITY_LOG_ENABLED=true
AZAMPAY_ACTIVITY_LOG_NAME=azampay
AZAMPAY_LOG_PAYMENT_EVENTS=true
AZAMPAY_LOG_WEBHOOK_EVENTS=true
AZAMPAY_LOG_ERROR_EVENTS=true
```

### 5. Include Routes

Add the following to your `routes/web.php` or `routes/api.php`:

```php
// Include AzamPay routes
require base_path('routes/azampay.php');
```

## Usage

### 1. Using the Service Container

```php
use Taitech\Azampay\AzampayService;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $azampay = app('azampay');
        
        $payload = [
            'amount' => 5000,
            'accountNumber' => '255713295803',
            'provider' => 'Tigo',
            'additionalProperties' => [
                'router' => "Home",
                'mac' => "A6:47:F6:52:38:D5",
            ],
        ];

        try {
            $response = $azampay->checkout($payload);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### 2. Using the Facade

```php
use Taitech\Azampay\Facades\Azampay;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $payload = [
            'amount' => 5000,
            'accountNumber' => '255713295803',
            'provider' => 'Tigo',
        ];

        try {
            $response = Azampay::checkout($payload);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

### 3. Using Dependency Injection

```php
use Taitech\Azampay\AzampayService;

class PaymentController extends Controller
{
    public function __construct(private AzampayService $azampay)
    {
        //
    }

    public function processPayment(Request $request)
    {
        $payload = $request->validate([
            'amount' => 'required|numeric|min:1',
            'accountNumber' => 'required|string',
            'provider' => 'required|string',
        ]);

        try {
            $response = $this->azampay->checkout($payload);
            return response()->json($response);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
```

## Webhook Handling

### 1. Automatic Webhook Processing

The package automatically handles webhooks at the configured route. Just ensure your webhook secret is set in the environment variables.

### 2. Custom Webhook Handling

If you need custom webhook logic, you can extend the `AzampayController`:

```php
use Taitech\Azampay\Controllers\AzampayController;

class CustomAzampayController extends AzampayController
{
    public function webhook(Request $request): JsonResponse
    {
        // Your custom logic here
        $result = parent::webhook($request);
        
        // Additional processing
        // ...
        
        return $result;
    }
}
```

## Configuration Options

### Available Configuration Keys

- `app_name`: Your AzamPay application name
- `client_id`: Your AzamPay client ID
- `secret`: Your AzamPay client secret
- `env`: Environment (SANDBOX/PRODUCTION)
- `default_service`: Default service type (MNO/BANK/MERCHANT)
- `timeout`: HTTP request timeout
- `keep_alive`: HTTP keep-alive setting
- `webhook_secret`: Secret for webhook signature verification
- `webhook_route`: Webhook endpoint route
- `default_currency`: Default currency (TZS)
- `enable_logging`: Enable/disable logging
- `log_channel`: Laravel log channel for AzamPay logs

### Environment-Specific Configuration

You can override any configuration value in your `.env` file by prefixing with `AZAMPAY_`.

## Logging

The package integrates with Laravel's logging system and Spatie's Activity Log package for comprehensive logging capabilities.

### Standard Logging

All AzamPay-related logs will be written to the configured log channel.

### Activity Log Integration

The package automatically integrates with Spatie's Activity Log package for structured, database-stored logging:

- **Payment Events**: Checkout attempts, responses, and errors
- **Webhook Events**: Incoming webhooks and processing results
- **Error Events**: Detailed error logging with context
- **Authentication Events**: Token generation and validation
- **Status Checks**: Payment status verification requests

#### Activity Log Features

- **Database Storage**: All activities are stored in the database
- **User Tracking**: Activities are linked to authenticated users
- **Structured Data**: Rich context data for each activity
- **Privacy Protection**: Sensitive data is automatically sanitized
- **Fallback Logging**: Graceful fallback to standard logging if Activity Log is unavailable

### Custom Log Channel

You can create a custom log channel in `config/logging.php`:

```php
'channels' => [
    'azampay' => [
        'driver' => 'daily',
        'path' => storage_path('logs/azampay.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
],
```

## Error Handling

The package throws exceptions for various error conditions. Always wrap your AzamPay calls in try-catch blocks:

```php
try {
    $response = $azampay->checkout($payload);
    // Handle success
} catch (Exception $e) {
    // Handle error
    Log::error('AzamPay error: ' . $e->getMessage());
}
```

## Testing

### 1. Mock the Service

```php
use Taitech\Azampay\AzampayService;

class PaymentTest extends TestCase
{
    public function test_payment_processing()
    {
        $this->mock(AzampayService::class, function ($mock) {
            $mock->shouldReceive('checkout')
                ->once()
                ->andReturn(['success' => true, 'reference' => 'test123']);
        });

        $response = $this->post('/azampay/checkout', [
            'amount' => 1000,
            'accountNumber' => '255123456789',
            'provider' => 'Tigo'
        ]);

        $response->assertStatus(200);
    }
}
```

### 2. Environment Testing

For testing, set `AZAMPAY_ENV=SANDBOX` in your `.env.testing` file.

### 3. Activity Log Usage

The package automatically logs all AzamPay activities. You can access the logs using Spatie's Activity Log:

```php
use Spatie\Activitylog\Models\Activity;

// Get all AzamPay activities
$activities = Activity::inLog('azampay')->get();

// Get payment checkout activities
$checkouts = Activity::inLog('azampay')
    ->whereJsonContains('properties->event_type', 'payment_checkout')
    ->get();

// Get activities for a specific user
$userActivities = Activity::inLog('azampay')
    ->causedBy($user)
    ->get();

// Get recent webhook activities
$webhooks = Activity::inLog('azampay')
    ->whereJsonContains('properties->event_type', 'webhook_received')
    ->latest()
    ->take(10)
    ->get();
```

## Security Considerations

1. **Webhook Verification**: Always verify webhook signatures using the configured secret
2. **Environment Variables**: Never commit sensitive credentials to version control
3. **HTTPS**: Use HTTPS in production for all payment-related endpoints
4. **Validation**: Always validate payment payloads before processing

## Troubleshooting

### Common Issues

1. **Service Not Found**: Ensure the service provider is registered
2. **Configuration Missing**: Check that all required environment variables are set
3. **Webhook Failures**: Verify webhook secret and route configuration
4. **Authentication Errors**: Confirm client ID and secret are correct
5. **Version Compatibility**: Ensure you're using Laravel 9.x, 10.x, or 11.x
6. **Composer Conflicts**: Check for package version conflicts

### Debug Mode

Enable debug logging by setting:

```env
AZAMPAY_ENABLE_LOGGING=true
LOG_LEVEL=debug
```

## Support

For issues or questions:
1. Check the package documentation
2. Review Laravel logs for error details
3. Open an issue on the GitHub repository
