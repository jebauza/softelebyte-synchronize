<?php

namespace Softelebyte\QueryFilters;

use Illuminate\Support\ServiceProvider;
use Softelebyte\QueryFilters\Commands\CreateFilterCommand;

class QueryFiltersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerConfig();
        $this->registerCommands();
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/query-filters.php', 'query-filters');
        $this->publishes([
            __DIR__ . '/../config/query-filters.php' => config_path('query-filters.php')
        ], 'config-query-filters');
    }

    private function registerCommands(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            CreateFilterCommand::class
        ]);
    }
}
