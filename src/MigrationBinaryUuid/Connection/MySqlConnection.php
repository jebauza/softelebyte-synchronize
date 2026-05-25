<?php


namespace Softelebyte\MigrationBinaryUuid\Connection;


use Softelebyte\MigrationBinaryUuid\Database\Schema\Blueprint;

class MySqlConnection extends \Illuminate\Database\MySqlConnection
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
