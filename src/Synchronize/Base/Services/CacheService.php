<?php


namespace Softelebyte\Synchronize\Base\Services;


use Illuminate\Support\Facades\Cache;
use Softelebyte\Synchronize\Logs\Repository\SyncStatusRepo;

class CacheService
{
    static function getSyncStatus()
    {
        return Cache::rememberForever('SyncStatus', function () {
            return SyncStatusRepo::GetStatus();
        });
    }
}
