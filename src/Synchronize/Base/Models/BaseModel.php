<?php


namespace Softelebyte\Synchronize\Base\Models;

class BaseModel extends BaseEtlModel
{
    const UPDATED = 'updated'; //Deprecated
    const ACTIVE_BE = 'active_be';

    public static function activeDefault()
    {
        return self::activeBe();
    }

    public static function activeBe()
    {
        $activeBe = config('synchronize.active_be');
        if ($activeBe) {
            return $activeBe;
        }
        return self::ACTIVE_BE;
    }
}
