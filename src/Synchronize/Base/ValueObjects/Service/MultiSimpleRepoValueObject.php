<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service;

use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Property;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\UpdateColumns;

abstract class MultiSimpleRepoValueObject implements SynchronizeValueObject, UpdateColumns, Property
{
}
