<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service;

use Softelebyte\Synchronize\Base\Contracts\ValueObjects\FromTableObject;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginModel;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Select;

abstract class SimpleTableValueObject implements FromTableObject, OriginModel, Select
{
}
