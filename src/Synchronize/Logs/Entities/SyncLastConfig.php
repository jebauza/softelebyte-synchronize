<?php

namespace Softelebyte\Synchronize\Logs\Entities;

use Softelebyte\Builder\Models\Model;
use Softelebyte\Builder\SoftelebyteBuilder;

class SyncLastConfig extends Model
{
    const TABLE = 'sync_last_configs';

    const CONFIG = 'config';
    const SYNC_LOG_ID = 'sync_log_id';

    protected $fillable = [self::CONFIG,self::SYNC_LOG_ID];

    protected $table = self::TABLE;
    public $incrementing = false;

    public function syncLog()
    {
        return $this->belongsTo(SyncLog::class);
    }

    public static function query(): SoftelebyteBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new SoftelebyteBuilder($query);
    }

    protected $primaryKey = self::CONFIG;
}
