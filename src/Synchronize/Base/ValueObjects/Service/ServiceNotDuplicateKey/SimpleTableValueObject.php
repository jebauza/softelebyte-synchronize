<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey;

use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Delete\SqlServer\DeleteValueObjectContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Insert;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginModel;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Select;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\ServiceNotDuplicateKey\BaseContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TargetKey;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\UpdateSet;

abstract class SimpleTableValueObject extends ServiceNotDuplicateKey implements BaseContract, OriginModel, Insert, Select, TargetKey, UpdateSet, DeleteValueObjectContract
{

}