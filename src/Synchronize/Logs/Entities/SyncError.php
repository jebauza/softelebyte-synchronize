<?php

namespace Softelebyte\Synchronize\Logs\Entities;

use Illuminate\Database\Eloquent\Model;
use Softelebyte\Builder\SoftelebyteBuilder;

class SyncError extends Model
{
    const TABLE = 'sync_errors';

    const ID = 'id';
    const SYNC_ID = 'sync_id';
    const ERROR = 'error';

    protected $table = self::TABLE;
    public $incrementing = false;

    public static function query(): SoftelebyteBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new SoftelebyteBuilder($query);
    }

    protected $primaryKey = 'Id';
}

