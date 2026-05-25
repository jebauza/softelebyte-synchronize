<?php


namespace Softelebyte\Synchronize\Base\Contracts\Row;


use Softelebyte\Synchronize\Base\Contracts\Actions\SynchronizeAction;
use Softelebyte\Synchronize\Base\Contracts\Container\SynchronizeContainerContract;
use Softelebyte\Synchronize\Logs\Contracts\LogService;

interface ActionChange
{
    public function action(LogService $logService, SynchronizeContainerContract $container): SynchronizeAction;
}
