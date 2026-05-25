<?php


namespace Softelebyte\Synchronize\Base\Contracts\Actions;


use Softelebyte\Synchronize\Base\Contracts\Container\SynchronizeContainerContract;
use Softelebyte\Synchronize\Base\Contracts\Row\RowClass;
use Softelebyte\Synchronize\Logs\Contracts\LogService;

interface SynchronizeAction
{
    public function __construct(LogService $logService, SynchronizeContainerContract $container);

    public function run(RowClass $rowClass);
}
