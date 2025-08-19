<?php

namespace Taitech\Azampay\Providers;

use Illuminate\Support\ServiceProvider;
use Taitech\Azampay\AzampayService;
use Taitech\Azampay\Modules\CallbackModule;

class AzampayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/../../config/azampay.php', 'azampay');

        // Register main Azampay service
        $this->app->singleton('azampay', function ($app) {
            return new AzampayService(
                config('azampay.app_name'),
                config('azampay.client_id'),
                config('azampay.secret'),
                config('azampay.env'),
                config('azampay.default_service')
            );
        });

        // Register callback module
        $this->app->singleton('azampay.callback', function ($app) {
            return new CallbackModule(config('azampay.webhook_secret'));
        });

        // Register activity logger service
        $this->app->singleton('azampay.logger', function ($app) {
            return new \Taitech\Azampay\Services\ActivityLoggerService();
        });

        // Register as facade alias
        $this->app->alias('azampay', AzampayService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/azampay.php' => config_path('azampay.php'),
            ], 'azampay-config');
        }

        // Publish routes if needed
        $this->publishes([
            __DIR__ . '/../../routes/azampay.php' => base_path('routes/azampay.php'),
        ], 'azampay-routes');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['azampay', 'azampay.callback', 'azampay.logger'];
    }
}
