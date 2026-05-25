<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Softelebyte\MigrationBinaryUuid\Database\Schema\Blueprint;

class CreateSyncLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->realUuid('id')->primary();
            $table->string('config');
            $table->dateTime('date_start');
            $table->dateTime('date_end')->nullable();
            $table->foreignRealUuid('sync_id')->constrained();
            $table->foreignRealUuid('sync_status_id')->constrained();
            $table->createdAt();
            $table->updatedAt();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_logs');
    }
}
