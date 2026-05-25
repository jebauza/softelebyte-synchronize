<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey;


use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginModel;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Select;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TruncateObject;

abstract class GroupTruncateValueObject implements TruncateObject, OriginModel, Select
{

}