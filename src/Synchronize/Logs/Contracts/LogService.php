<?php


namespace Softelebyte\Synchronize\Logs\Contracts;


use Carbon\Carbon;
use Exception;

interface LogService
{
    public function start(Carbon $syncDate): void;

    public function startConfig(string $configName): void;

    public function endConfig(): void;

    public function end(): void;

    public function reportError(Exception $exception): void;
}
