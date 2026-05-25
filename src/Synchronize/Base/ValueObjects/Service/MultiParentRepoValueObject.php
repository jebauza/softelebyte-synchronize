<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service;

use Softelebyte\Synchronize\Base\Contracts\ValueObjects\BaseObjects;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\MultiRepoValueObjectContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\UpdateColumns;

abstract class MultiParentRepoValueObject implements SynchronizeValueObject, MultiRepoValueObjectContract, UpdateColumns, BaseObjects
{
}
