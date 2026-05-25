<?php

namespace Softelebyte\Synchronize\Base\ValueObjects\Iterator;

class IteratorValueObject
{
    protected string $originName;
    protected string $destinyName;
    protected $defaultValue;
    protected $convertFunction;

    public function __construct(string $originName, string $destinyName = null, $defaultValue = null, callable $convertFunction = null)
    {
        $this->originName = $originName;
        $this->destinyName = $destinyName ?? $originName;
        $this->defaultValue = $defaultValue;
        $this->convertFunction = $convertFunction ??
            function ($value) {
                return $value;
            };
    }

    public function getOriginName(): string
    {
        return $this->originName;
    }

    public function getDestinyName(): string
    {
        return $this->destinyName;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getConvertFunction(): callable
    {
        return $this->convertFunction;
    }
}
