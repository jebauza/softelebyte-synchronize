<?php

namespace Softelebyte\Synchronize\Logs\Entities;

use Illuminate\Database\Eloquent\Model;
use Softelebyte\Builder\SoftelebyteBuilder;

class Sync extends Model
{
    const TABLE = 'syncs';

    const ID = 'id';
    const SYNC_DATA = 'sync_data';
    const SYNC_STATUS_ID = 'sync_status_id';

    protected $table = self::TABLE;
    public $incrementing = false;

    public function syncStatus()
    {
        return $this->belongsTo(SyncStatus::class);
    }

    public function syncErrors()
    {
        return $this->hasMany(SyncError::class);
    }

    public static function query(): SoftelebyteBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new SoftelebyteBuilder($query);
    }

    protected $primaryKey = self::ID;
}
