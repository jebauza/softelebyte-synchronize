<?php


namespace Softelebyte\MigrationBinaryUuid\Connection;


use Softelebyte\MigrationBinaryUuid\Database\Schema\Blueprint;

class SQLiteConnection extends \Illuminate\Database\SQLiteConnection
{
    public function getSchemaBuilder()
    {
        $builder = parent::getSchemaBuilder();
        $builder->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });
        return $builder;
    }
}
