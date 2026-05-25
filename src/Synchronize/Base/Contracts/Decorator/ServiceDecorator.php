<?php


namespace Softelebyte\Synchronize\Base\Contracts\Decorator;


use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;

interface ServiceDecorator
{
    public function instanceService(): ServiceContract;
}