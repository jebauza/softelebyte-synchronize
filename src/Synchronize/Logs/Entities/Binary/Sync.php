<?php

namespace Softelebyte\Synchronize\Logs\Entities\Binary;

use Dyrynda\Database\Support\GeneratesUuid;
use Softelebyte\MigrationBinaryUuid\Casts\EfficientUuid;
use Softelebyte\Synchronize\Logs\Entities\Sync as SyncBase;

class Sync extends SyncBase
{
    use GeneratesUuid;

    protected $casts = [
        self::ID => EfficientUuid::class,
        self::SYNC_STATUS_ID => EfficientUuid::class,
    ];

    public function uuidColumns(): array
    {
        return [self::ID, self::SYNC_STATUS_ID];
    }
}
