<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


interface SynchronizeValueObject
{
    public function targetModel(): string;
}
