<?php


namespace Softelebyte\Synchronize\Base\Contracts\Container;


use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;

interface SynchronizeContainerContract
{
    public function addNewServiceToContainer(string $valueObject, string $storeService);

    public function getService(string $thisValueObject): ServiceContract;
}