<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Service;

use Softelebyte\Synchronize\Base\Contracts\ValueObjects\FromTableObject;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\OriginName;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Select;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SubFromQuery;

abstract class FromSubTableValueObject implements FromTableObject, Select, SubFromQuery, OriginName
{
}
