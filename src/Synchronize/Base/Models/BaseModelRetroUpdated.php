<?php


namespace Softelebyte\Synchronize\Base\Models;

class BaseModelRetroUpdated extends BaseModel
{
    public static function activeDefault()
    {
        return self::activeBe();
    }

    public static function activeBe()
    {
        return self::ACTIVE;
    }

    public static function activeEtl()
    {
        return self::UPDATED;
    }
}
