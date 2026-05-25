<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo;


use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginKey;

interface SelectOriginKeys
{
    public function processKeys(OriginKey $object): array;
}