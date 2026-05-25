<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Softelebyte\MigrationBinaryUuid\Database\Schema\Blueprint;
use Softelebyte\Synchronize\Logs\database\seeders\SyncStatusSeeder;

class CreateSyncStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_statuses', function (Blueprint $table) {
            $table->realUuid('id')->primary();
            $table->string('name');
            $table->createdAt();
            $table->updatedAt();
        });

        (new SyncStatusSeeder())->run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_statuses');
    }
}
