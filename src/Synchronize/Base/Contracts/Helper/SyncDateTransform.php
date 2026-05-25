<?php


namespace Softelebyte\Synchronize\Base\Contracts\Helper;


use Carbon\Carbon;

interface SyncDateTransform
{
    public function syncDateTransform(Carbon $syncDate);
}
