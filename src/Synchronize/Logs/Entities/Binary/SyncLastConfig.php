<?php

namespace Softelebyte\Synchronize\Logs\Entities\Binary;

use Dyrynda\Database\Support\GeneratesUuid;
use Softelebyte\MigrationBinaryUuid\Casts\EfficientUuid;
use Softelebyte\Synchronize\Logs\Entities\SyncLastConfig as SyncLastConfigBase;

class SyncLastConfig extends SyncLastConfigBase
{
    use GeneratesUuid;

    protected $casts = [
        self::SYNC_LOG_ID => EfficientUuid::class,
    ];

    public function uuidColumns(): array
    {
        return [self::SYNC_LOG_ID];
    }
}
