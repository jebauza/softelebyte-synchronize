<?php

namespace Softelebyte\Synchronize\Logs\Entities\Binary;

use Dyrynda\Database\Support\GeneratesUuid;
use Softelebyte\MigrationBinaryUuid\Casts\EfficientUuid;
use Softelebyte\Synchronize\Logs\Entities\SyncStatus as SyncStatusBase;

class SyncStatus extends SyncStatusBase
{
    use GeneratesUuid;

    protected $casts = [
        'id' => EfficientUuid::class,
    ];

    public function uuidColumn(): string
    {
        return 'id';
    }
}
