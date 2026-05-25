<?php

namespace Softelebyte\Synchronize\Logs\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Softelebyte\MigrationBinaryUuid\Connection\PostgresConnection;
use Softelebyte\MigrationBinaryUuid\Connection\SqlServerConnection;

class SyncStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $uuidFunction = function() {
            return Str::uuid();
        };
        if(!(DB::connection() instanceof SqlServerConnection) && !(DB::connection() instanceof PostgresConnection)) {
            $uuidFunction = function() {
                return hex2bin(str_replace('-', '', Str::uuid()));
            };
        }
        DB::table('sync_statuses')->insert([
            [
                'id' => $uuidFunction(),
                'name' => 'Inactivo'
            ],
            [
                'id' => $uuidFunction(),
                'name' => 'Ejecutando'
            ],
            [
                'id' => $uuidFunction(),
                'name' => 'Error'
            ],
            [
                'id' => $uuidFunction(),
                'name' => 'Finalizado'
            ]
        ]);
    }
}
