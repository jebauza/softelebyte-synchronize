<?php

namespace Softelebyte\Synchronize\Logs\Entities\Binary;

use Dyrynda\Database\Support\GeneratesUuid;
use Softelebyte\MigrationBinaryUuid\Casts\EfficientUuid;
use Softelebyte\Synchronize\Logs\Entities\SyncError as SyncErrorBase;

class SyncError extends SyncErrorBase
{
    use GeneratesUuid;

    protected $casts = [
        'id' => EfficientUuid::class,
        'sync_id' => EfficientUuid::class,
    ];

    public function uuidColumns(): array
    {
        return ['id', 'sync_id'];
    }
}

