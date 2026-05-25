<?php


namespace Softelebyte\OutputHelper\Console\Commands;


use Illuminate\Console\Command;
use Softelebyte\OutputHelper\Services\ExampleService;

class OutputHelperTestCommand extends Command
{
    protected $signature = 'outputHelper:test';

    protected $description = 'Este comando imprime por pantalla una prueba de OutputHelper';

    public function handle()
    {
        $service = new ExampleService();
        $service->handle();
    }
}