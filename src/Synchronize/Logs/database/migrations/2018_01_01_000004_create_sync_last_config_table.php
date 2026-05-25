<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Softelebyte\MigrationBinaryUuid\Database\Schema\Blueprint;

class CreateSyncLastConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_last_configs', function (Blueprint $table) {
            $table->string('config')->primary();
            $table->foreignRealUuid('sync_log_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('sync_last_configs');
    }
}
