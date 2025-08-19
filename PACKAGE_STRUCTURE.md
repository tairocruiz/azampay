# Azampay Package Structure

This document outlines the structure of the Azampay package and how it integrates with Laravel applications.

## Directory Structure

```
azampay/
├── config/
│   └── azampay.php                 # Laravel configuration file
├── routes/
│   └── azampay.php                 # Laravel routes file
├── src/
│   ├── AzampayService.php          # Main service class
│   ├── Controllers/
│   │   └── AzampayController.php   # Laravel controller
│   ├── Facades/
│   │   └── AzampayFacade.php       # Laravel facade
│   ├── Helpers/
│   │   └── MNOPayloadValidator.php # Validation trait
│   ├── Modules/
│   │   ├── AzampayModule.php       # Base module class
│   │   └── CallbackModule.php      # Callback handling
│   ├── Providers/
│   │   └── AzampayServiceProvider.php # Laravel service provider
│   └── Services/
│       ├── ActivityLoggerService.php # Activity logging service
│       ├── BankServices.php        # Bank payment services
│       ├── MerchantServices.php    # Merchant services
│       └── MNOServices.php         # Mobile money services
├── tests/
│   └── AzampayServiceTest.php      # Unit tests
├── composer.json                    # Package configuration
├── LARAVEL_INTEGRATION.md          # Laravel integration guide
├── PACKAGE_STRUCTURE.md            # This file
├── env.example                     # Environment variables example
└── README.md                       # Main documentation
```

## Laravel Integration Components

### 1. Service Provider (`src/Providers/AzampayServiceProvider.php`)
- **Purpose**: Registers services with Laravel's service container
- **Features**:
  - Automatic service registration
  - Configuration merging
  - Facade binding
  - Asset publishing

### 2. Facade (`src/Facades/AzampayFacade.php`)
- **Purpose**: Provides static access to Azampay services
- **Usage**: `Azampay::checkout($payload)`
- **Benefits**: Clean, readable syntax

### 3. Controller (`src/Controllers/AzampayController.php`)
- **Purpose**: Handles HTTP requests for payments and webhooks
- **Features**:
  - Webhook processing
  - Payment checkout
  - Status checking
  - Laravel validation
  - Integrated logging

### 4. Configuration (`config/azampay.php`)
- **Purpose**: Centralized configuration management
- **Features**:
  - Environment variable integration
  - Sensible defaults
  - Easy customization

### 5. Routes (`routes/azampay.php`)
- **Purpose**: Pre-defined API endpoints
- **Endpoints**:
  - `POST /azampay/webhook` - Webhook handling
  - `POST /azampay/checkout` - Payment processing
  - `GET /azampay/status/{reference}` - Status checking

## Service Architecture

### Core Services
1. **AzampayService**: Main entry point and service factory
2. **MNOServices**: Mobile money payment processing
3. **BankServices**: Bank payment processing (placeholder)
4. **MerchantServices**: Merchant services (placeholder)

### Support Classes
1. **AzampayModule**: Base class with HTTP client and authentication
2. **CallbackModule**: Webhook signature verification and processing
3. **MNOPayloadValidator**: Input validation and sanitization

## Laravel-Specific Features

### 1. Automatic Service Discovery
- Package automatically registers with Laravel
- No manual service provider registration needed
- Facade automatically available

### 2. Configuration Publishing
```bash
php artisan vendor:publish --tag=azampay-config
```

### 3. Route Publishing
```bash
php artisan vendor:publish --tag=azampay-routes
```

### 4. Environment Integration
- All configuration values can be overridden via `.env`
- Environment-specific settings
- Secure credential management

### 5. Logging Integration
- Uses Laravel's logging system
- Configurable log channels
- Structured logging for debugging
- **Activity Log Integration**: Database-stored activities with Spatie
- **Unauthenticated Support**: Handles API requests without user sessions
- **Rich Context**: IP addresses, user agents, request IDs, and metadata

## Usage Patterns

### 1. Service Container
```php
$azampay = app('azampay');
$response = $azampay->checkout($payload);
```

### 2. Facade
```php
use Taitech\Azampay\Facades\Azampay;
$response = Azampay::checkout($payload);
```

### 3. Dependency Injection
```php
public function __construct(private AzampayService $azampay) {}
$response = $this->azampay->checkout($payload);
```

### 4. Direct Instantiation
```php
$azampay = new AzampayService($app, $clientId, $secret, $env, $service);
$response = $azampay->checkout($payload);
```

## Configuration Options

| Key | Description | Default | Required |
|-----|-------------|---------|----------|
| `app_name` | AzamPay application name | - | Yes |
| `client_id` | AzamPay client ID | - | Yes |
| `secret` | AzamPay client secret | - | Yes |
| `env` | Environment (SANDBOX/PRODUCTION) | SANDBOX | No |
| `default_service` | Service type (MNO/BANK/MERCHANT) | MNO | No |
| `webhook_secret` | Webhook signature verification | - | Yes |
| `webhook_route` | Webhook endpoint route | azampay/webhook | No |
| `enable_logging` | Enable/disable logging | true | No |
| `log_channel` | Laravel log channel | azampay | No |

## Security Features

1. **HMAC Signature Verification**: Webhook security
2. **Environment Isolation**: Sandbox vs production
3. **Input Validation**: Payload sanitization
4. **Secure Credential Storage**: Environment variables only
5. **HTTPS Enforcement**: Production security

## Testing

### 1. Unit Tests
- Service initialization
- Method proxying
- Error handling
- Validation logic

### 2. Laravel Testing
- Service mocking
- Route testing
- Controller testing
- Integration testing

## Deployment Considerations

1. **Environment Variables**: Set all required credentials
2. **Webhook URLs**: Configure AzamPay webhook endpoints
3. **HTTPS**: Use HTTPS in production
4. **Logging**: Configure appropriate log levels
5. **Monitoring**: Set up error monitoring and alerting

## Support and Maintenance

- **Documentation**: Comprehensive Laravel integration guide
- **Examples**: Working code samples
- **Testing**: Unit test coverage
- **Error Handling**: Comprehensive exception handling
- **Logging**: Detailed logging for debugging
