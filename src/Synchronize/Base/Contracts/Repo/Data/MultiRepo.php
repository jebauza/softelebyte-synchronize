<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo\Data;


use Softelebyte\Synchronize\Base\ValueObjects\Data\BaseMultiValueObject;

interface MultiRepo
{
    public function getAll(): BaseMultiValueObject;
}
