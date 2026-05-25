<?php

namespace Softelebyte\Synchronize\Logs\Providers;


use Illuminate\Support\ServiceProvider;
use Softelebyte\Synchronize\Logs\Contracts\LogService;
use Softelebyte\Synchronize\Logs\Service\SyncLogService;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->app->singleton(LogService::class, function ($app) {
            return $app->make(SyncLogService::class);
        });

        $this->publishes(
            [
                __DIR__ . '/../Resources/views/mail/synchronize/error.blade.php' => resource_path('views/mail/synchronize/error.blade.php'),
            ]
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
