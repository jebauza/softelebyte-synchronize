<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects\ServiceNotDuplicateKey;

use Softelebyte\Synchronize\Base\Contracts\ValueObjects\On;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginKey;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginName;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TargetName;

interface BaseContract extends On, OriginName, OriginKey, TargetName, SynchronizeValueObject
{

}