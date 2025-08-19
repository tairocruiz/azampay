# Version Compatibility Matrix

This document outlines the compatibility between the Azampay package and different versions of Laravel and PHP.

## Laravel Version Support

| Laravel Version | Status | PHP Version | Notes |
|----------------|---------|-------------|-------|
| **9.x** | ✅ **Supported** | ^8.1 | LTS Release |
| **10.x** | ✅ **Supported** | ^8.1 | LTS Release |
| **11.x** | ✅ **Supported** | ^8.1 | Latest Release |

## PHP Version Support

| PHP Version | Status | Laravel Support | Notes |
|-------------|---------|-----------------|-------|
| **8.1** | ✅ **Supported** | 9.x, 10.x, 11.x | Minimum Required |
| **8.2** | ✅ **Supported** | 9.x, 10.x, 11.x | Recommended |
| **8.3** | ✅ **Supported** | 10.x, 11.x | Latest Stable |

## Package Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| **guzzlehttp/guzzle** | ^7.5 | HTTP client for API calls |
| **illuminate/support** | ^9.0\|^10.0\|^11.0 | Laravel support features |
| **illuminate/routing** | ^9.0\|^10.0\|^11.0 | Laravel routing system |
| **illuminate/http** | ^9.0\|^10.0\|^11.0 | Laravel HTTP handling |
| **illuminate/config** | ^9.0\|^10.0\|^11.0 | Laravel configuration |

## Compatibility Notes

### Laravel 9.x
- **Release Date**: February 2022
- **End of Life**: August 2024
- **PHP Support**: 8.1+
- **Features**: Full package support

### Laravel 10.x
- **Release Date**: February 2023
- **End of Life**: August 2025
- **PHP Support**: 8.1+
- **Features**: Full package support

### Laravel 11.x
- **Release Date**: February 2024
- **End of Life**: August 2026
- **PHP Support**: 8.1+
- **Features**: Full package support

## Installation Commands

### Laravel 9.x
```bash
composer require taitech/azampay:^1.0
```

### Laravel 10.x
```bash
composer require taitech/azampay:^1.0
```

### Laravel 11.x
```bash
composer require taitech/azampay:^1.0
```

## Testing Compatibility

### PHPUnit Testing
```bash
# Test with PHP 8.1
php8.1 vendor/bin/phpunit

# Test with PHP 8.2
php8.2 vendor/bin/phpunit

# Test with PHP 8.3
php8.3 vendor/bin/phpunit
```

### Laravel Testing
```bash
# Test with Laravel 9.x
php artisan test

# Test with Laravel 10.x
php artisan test

# Test with Laravel 11.x
php artisan test
```

## Breaking Changes

### Version 1.0.0
- **Initial Release**: No breaking changes
- **Laravel Support**: 9.x, 10.x, 11.x
- **PHP Support**: 8.1+

## Future Compatibility

### Planned Support
- **Laravel 12.x**: Will be supported when released
- **PHP 8.4**: Will be supported when stable
- **PHP 9.0**: Will be evaluated for compatibility

### Deprecation Policy
- **Laravel Versions**: Support for 2 major versions
- **PHP Versions**: Support for 2 major versions
- **Advance Notice**: 6 months before dropping support

## Troubleshooting

### Common Compatibility Issues

1. **Composer Conflicts**
   ```bash
   composer why-not taitech/azampay
   ```

2. **Version Mismatch**
   ```bash
   composer show laravel/framework
   composer show taitech/azampay
   ```

3. **PHP Version Issues**
   ```bash
   php --version
   composer check-platform-reqs
   ```

### Resolution Steps

1. **Update Laravel**: Ensure you're using a supported version
2. **Update PHP**: Ensure you're using PHP 8.1 or higher
3. **Clear Cache**: `composer clear-cache`
4. **Update Dependencies**: `composer update`

## Support

For compatibility issues:
1. Check this compatibility matrix
2. Review Laravel version requirements
3. Check PHP version requirements
4. Open an issue on GitHub with version details
