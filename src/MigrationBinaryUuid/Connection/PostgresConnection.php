<?php


namespace Softelebyte\MigrationBinaryUuid\Connection;


use Softelebyte\MigrationBinaryUuid\Database\Schema\Blueprint;

class PostgresConnection extends \Illuminate\Database\PostgresConnection
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
