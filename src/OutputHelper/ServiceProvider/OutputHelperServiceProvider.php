<?php

namespace Softelebyte\OutputHelper\ServiceProvider;

use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Softelebyte\OutputHelper\Console\Commands\OutputHelperTestCommand;
use Softelebyte\OutputHelper\Fields\OutputHelperFields;

class OutputHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(OutputHelperFields::CONSOLE_OUTPUT, function () {
            return new OutputStyle(
                new StringInput(''),
                new StreamOutput(fopen('php://stdout', 'w'))
            );
        });
    }

    public function register()
    {
        $this->commands([
            OutputHelperTestCommand::class
        ]);
    }
}