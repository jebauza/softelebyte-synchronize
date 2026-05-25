<?php


namespace Softelebyte\Synchronize\Base\Providers;


use Illuminate\Support\ServiceProvider;
use Softelebyte\Synchronize\Base\Command\PrintConfigCommand;
use Softelebyte\Synchronize\Base\Command\SynchronizeDataCommand;
use Softelebyte\Synchronize\Base\Container\SynchronizeContainer;
use Softelebyte\Synchronize\Base\Contracts\Container\OptionsRequestContract;
use Softelebyte\Synchronize\Base\Contracts\Container\SynchronizeContainerContract;
use Softelebyte\Synchronize\Base\Contracts\Services\SynchronizeDataServiceContract;
use Softelebyte\Synchronize\Base\Services\SynchronizeDataService;
use Softelebyte\Synchronize\Base\ValueObjects\Options\OptionsRequest;

class SynchronizeDataServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            SynchronizeDataCommand::class,
            PrintConfigCommand::class
        ]);

        $this->app->singleton(SynchronizeContainerContract::class, function ($app) {
            return $app->make(SynchronizeContainer::class);
        });

        $this->app->singleton(OptionsRequestContract::class, function ($app) {
            return $app->make(OptionsRequest::class);
        });

        $this->app->bind(SynchronizeDataServiceContract::class, SynchronizeDataService::class);

        $this->publishes(
            [
                __DIR__ . '/../Config/synchronize.php' => config_path('synchronize.php'),
            ]);
    }
}
