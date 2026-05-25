<?php

namespace Softelebyte\Synchronize\Logs\Entities;

use Illuminate\Database\Eloquent\Model;
use Softelebyte\Builder\SoftelebyteBuilder;

class SyncStatus extends Model
{
    const TABLE = 'sync_statuses';

    const ID = 'id';
    const NAME = 'name';

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

    protected $primaryKey = 'id';
}
