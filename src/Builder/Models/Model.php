<?php


namespace Softelebyte\Builder\Models;


use Softelebyte\Builder\SoftelebyteBuilder;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public static function query(): SoftelebyteBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query)
    {
        return new SoftelebyteBuilder($query);
    }

    public function getCompleteTableName(): string
    {
        return self::query()->getCompleteTableName();
    }
}