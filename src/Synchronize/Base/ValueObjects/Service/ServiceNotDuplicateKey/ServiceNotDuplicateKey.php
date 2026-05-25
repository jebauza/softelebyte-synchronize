<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey;


use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginKey;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TargetKey;

abstract class ServiceNotDuplicateKey implements OriginKey, TargetKey
{

    public function firstOriginKey()
    {
        return $this->originKeyPosition();
    }

    public function originKeyPosition(int $position = 0): string
    {
        return $this->originKey()[$position];
    }

    public function firstTargetKey()
    {
        return $this->targetKeyPosition();
    }

    public function targetKeyPosition(int $position = 0): string
    {
        return $this->targetKey()[$position];
    }
}