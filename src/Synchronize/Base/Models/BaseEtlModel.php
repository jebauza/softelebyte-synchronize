<?php


namespace Softelebyte\Synchronize\Base\Models;

use Softelebyte\Builder\Models\Model;
use Softelebyte\Synchronize\Base\Scopes\Active;

class BaseEtlModel extends Model
{
    const ACTIVE = 'active'; //Deprecated
    const ACTIVE_ETL = 'active_etl';

    public static function activeDefault()
    {
        return self::activeEtl();
    }

    public static function activeEtl()
    {
        $activeEtl = config('synchronize.active_etl');
        if ($activeEtl) {
            return $activeEtl;
        }
        return self::ACTIVE_ETL;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new Active());
    }

}
