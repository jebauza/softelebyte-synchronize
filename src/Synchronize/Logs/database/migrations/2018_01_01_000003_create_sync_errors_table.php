<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Softelebyte\MigrationBinaryUuid\Database\Schema\Blueprint;

class CreateSyncErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_errors', function (Blueprint $table) {
            $table->realUuid('id')->primary();
            $table->foreignRealUuid('sync_id')->constrained();
            $table->longText('error');
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
        Schema::dropIfExists('sync_errors');
    }
}
