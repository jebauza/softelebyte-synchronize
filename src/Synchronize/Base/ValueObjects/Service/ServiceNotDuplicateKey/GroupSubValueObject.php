<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey;

use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Delete\SqlServer\DeleteSubContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Insert;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SelectInsert;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\ServiceNotDuplicateKey\BaseContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SubFromQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TargetKey;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\UpdateSet;

abstract class GroupSubValueObject extends ServiceNotDuplicateKey implements BaseContract, Insert, SelectInsert, TargetKey, UpdateSet, SubFromQuery, DeleteSubContract
{
}