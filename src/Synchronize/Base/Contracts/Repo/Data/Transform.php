<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo\Data;


use Softelebyte\Synchronize\Base\ValueObjects\Iterator\IteratorValueObject;

interface Transform
{
    /**
     * @return IteratorValueObject[]
     */
    public function transform(): array;
}
