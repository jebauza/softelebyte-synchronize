<?php


namespace Softelebyte\Synchronize\Base\Contracts\Services;


interface ServiceContract
{
    public function handle();

    public function getIsSameCount(): bool;
}
