<?php

namespace Taitech\Azampay\Services;

use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Log;

class ActivityLoggerService
{
    private ActivityLogger $activityLogger;
    private bool $enabled;
    private string $logName;

    public function __construct()
    {
        $this->enabled = config('azampay.activity_log_enabled', true);
        $this->logName = config('azampay.activity_log_name', 'azampay');
        
        if ($this->enabled && class_exists(ActivityLogger::class)) {
            $this->activityLogger = activity($this->logName);
        }
    }

    /**
     * Log payment checkout event
     */
    public function logPaymentCheckout(array $payload, $response = null, $userId = null, array $context = []): void
    {
        if (!$this->enabled || !config('azampay.log_payment_events', true)) {
            return;
        }

        try {
            $logger = $this->activityLogger;
            
            // Only set causedBy if user is authenticated
            if ($userId) {
                $logger = $logger->causedBy($userId);
            }
            
            $logger->withProperties([
                'event_type' => 'payment_checkout',
                'payload' => $this->sanitizePayload($payload),
                'response' => $response,
                'timestamp' => now()->toISOString(),
                'authenticated' => !empty($userId),
                'ip_address' => $context['ip_address'] ?? null,
                'user_agent' => $context['user_agent'] ?? null,
                'request_id' => $context['request_id'] ?? null,
            ])
            ->log('Payment checkout initiated');
        } catch (\Exception $e) {
            // Fallback to regular logging if activity log fails
            Log::channel(config('azampay.log_channel', 'azampay'))
                ->info('Payment checkout initiated', [
                    'payload' => $payload,
                    'response' => $response,
                    'user_id' => $userId,
                    'authenticated' => !empty($userId),
                    'context' => $context
                ]);
        }
    }

    /**
     * Log webhook event
     */
    public function logWebhookEvent(array $headers, $body, $status = 'success', $userId = null, array $context = []): void
    {
        if (!$this->enabled || !config('azampay.log_webhook_events', true)) {
            return;
        }

        try {
            $logger = $this->activityLogger;
            
            // Only set causedBy if user is authenticated
            if ($userId) {
                $logger = $logger->causedBy($userId);
            }
            
            $logger->withProperties([
                'event_type' => 'webhook_received',
                'headers' => $this->sanitizeHeaders($headers),
                'body' => $body,
                'status' => $status,
                'timestamp' => now()->toISOString(),
                'authenticated' => !empty($userId),
                'ip_address' => $context['ip_address'] ?? null,
                'user_agent' => $context['user_agent'] ?? null,
                'request_id' => $context['request_id'] ?? null,
                'source' => $context['source'] ?? 'azampay',
            ])
            ->log('Webhook received and processed');
        } catch (\Exception $e) {
            // Fallback to regular logging if activity log fails
            Log::channel(config('azampay.log_channel', 'azampay'))
                ->info('Webhook received and processed', [
                    'headers' => $headers,
                    'body' => $body,
                    'status' => $status,
                    'user_id' => $userId,
                    'authenticated' => !empty($userId),
                    'context' => $context
                ]);
        }
    }

    /**
     * Log error event
     */
    public function logError(string $error, array $context = [], $userId = null): void
    {
        if (!$this->enabled || !config('azampay.log_error_events', true)) {
            return;
        }

        try {
            $logger = $this->activityLogger;
            
            // Only set causedBy if user is authenticated
            if ($userId) {
                $logger = $logger->causedBy($userId);
            }
            
            $logger->withProperties([
                'event_type' => 'error',
                'error_message' => $error,
                'context' => $context,
                'timestamp' => now()->toISOString(),
                'authenticated' => !empty($userId),
                'ip_address' => $context['ip_address'] ?? null,
                'user_agent' => $context['user_agent'] ?? null,
                'request_id' => $context['request_id'] ?? null,
            ])
            ->log('Error occurred in Azampay service');
        } catch (\Exception $e) {
            // Fallback to regular logging if activity log fails
            Log::channel(config('azampay.log_channel', 'azampay'))
                ->error('Error occurred in Azampay service', [
                    'error' => $error,
                    'context' => $context,
                    'user_id' => $userId,
                    'authenticated' => !empty($userId)
                ]);
        }
    }

    /**
     * Log payment status check
     */
    public function logStatusCheck(string $reference, $status = null, $userId = null, array $context = []): void
    {
        if (!$this->enabled || !config('azampay.log_payment_events', true)) {
            return;
        }

        try {
            $logger = $this->activityLogger;
            
            // Only set causedBy if user is authenticated
            if ($userId) {
                $logger = $logger->causedBy($userId);
            }
            
            $logger->withProperties([
                'event_type' => 'status_check',
                'reference' => $reference,
                'status' => $status,
                'timestamp' => now()->toISOString(),
                'authenticated' => !empty($userId),
                'ip_address' => $context['ip_address'] ?? null,
                'user_agent' => $context['user_agent'] ?? null,
                'request_id' => $context['request_id'] ?? null,
            ])
            ->log('Payment status checked');
        } catch (\Exception $e) {
            // Fallback to regular logging if activity log fails
            Log::channel(config('azampay.log_channel', 'azampay'))
                ->info('Payment status checked', [
                    'reference' => $reference,
                    'status' => $status,
                    'user_id' => $userId,
                    'authenticated' => !empty($userId),
                    'context' => $context
                ]);
        }
    }

    /**
     * Log authentication event
     */
    public function logAuthentication(string $event, array $context = [], $userId = null): void
    {
        if (!$this->enabled) {
            return;
        }

        try {
            $logger = $this->activityLogger;
            
            // Only set causedBy if user is authenticated
            if ($userId) {
                $logger = $logger->causedBy($userId);
            }
            
            $logger->withProperties([
                'event_type' => 'authentication',
                'auth_event' => $event,
                'context' => $context,
                'timestamp' => now()->toISOString(),
                'authenticated' => !empty($userId),
                'ip_address' => $context['ip_address'] ?? null,
                'user_agent' => $context['user_agent'] ?? null,
                'request_id' => $context['request_id'] ?? null,
            ])
            ->log("Authentication event: {$event}");
        } catch (\Exception $e) {
            // Fallback to regular logging if activity log fails
            Log::channel(config('azampay.log_channel', 'azampay'))
                ->info("Authentication event: {$event}", [
                    'context' => $context,
                    'user_id' => $userId,
                    'authenticated' => !empty($userId)
                ]);
        }
    }

    /**
     * Sanitize payload for logging (remove sensitive data)
     */
    private function sanitizePayload(array $payload): array
    {
        $sanitized = $payload;
        
        // Remove or mask sensitive fields
        if (isset($sanitized['accountNumber'])) {
            $sanitized['accountNumber'] = $this->maskPhoneNumber($sanitized['accountNumber']);
        }
        
        if (isset($sanitized['additionalProperties'])) {
            $sanitized['additionalProperties'] = '***REDACTED***';
        }
        
        return $sanitized;
    }

    /**
     * Sanitize headers for logging (remove sensitive data)
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = $headers;
        
        // Remove sensitive headers
        $sensitiveHeaders = ['authorization', 'x-signature', 'x-api-key'];
        
        foreach ($sensitiveHeaders as $header) {
            if (isset($sanitized[$header])) {
                $sanitized[$header] = '***REDACTED***';
            }
        }
        
        return $sanitized;
    }

    /**
     * Mask phone number for privacy
     */
    private function maskPhoneNumber(string $phone): string
    {
        if (strlen($phone) <= 4) {
            return str_repeat('*', strlen($phone));
        }
        
        return substr($phone, 0, 2) . str_repeat('*', strlen($phone) - 4) . substr($phone, -2);
    }

    /**
     * Check if activity logging is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled && class_exists(ActivityLogger::class);
    }

    /**
     * Get the current log name
     */
    public function getLogName(): string
    {
        return $this->logName;
    }

    /**
     * Extract request context for logging
     */
    public function extractRequestContext($request = null): array
    {
        $context = [];
        
        if ($request) {
            $context['ip_address'] = $request->ip();
            $context['user_agent'] = $request->userAgent();
            $context['request_id'] = $request->header('X-Request-ID') ?? uniqid('req_', true);
            $context['method'] = $request->method();
            $context['url'] = $request->fullUrl();
        } else {
            $context['ip_address'] = request()->ip() ?? null;
            $context['user_agent'] = request()->userAgent() ?? null;
            $context['request_id'] = request()->header('X-Request-ID') ?? uniqid('req_', true);
            $context['method'] = request()->method() ?? null;
            $context['url'] = request()->fullUrl() ?? null;
        }
        
        return $context;
    }

    /**
     * Get current user ID safely (handles unauthenticated requests)
     */
    public function getCurrentUserId()
    {
        try {
            return auth()->id();
        } catch (\Exception $e) {
            return null;
        }
    }
}
