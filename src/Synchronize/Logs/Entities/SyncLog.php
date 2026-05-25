<?php

namespace Softelebyte\Synchronize\Logs\Entities;

use Illuminate\Database\Eloquent\Model;
use Softelebyte\Builder\SoftelebyteBuilder;

class SyncLog extends Model
{
    const TABLE = 'sync_logs';

    const ID = 'id';
    const CONFIG = 'config';
    const SYNC_ID = 'sync_id';
    const SYNC_STATUS_ID = 'sync_status_id';
    const DATE_START = 'date_start';
    const DATE_END = 'date_end';

    protected $table = self::TABLE;
    public $incrementing = false;

    public static function query(): SoftelebyteBuilder
    {
        return parent::query();
    }

    public function sync()
    {
        return $this->belongsTo(Sync::class);
    }

    public function newEloquentBuilder($query)
    {
        return new SoftelebyteBuilder($query);
    }

    protected $primaryKey = self::ID;

}
