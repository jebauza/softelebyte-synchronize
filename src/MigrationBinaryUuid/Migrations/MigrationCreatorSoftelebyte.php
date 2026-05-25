<?php

namespace Softelebyte\MigrationBinaryUuid\Migrations;


use Illuminate\Database\Migrations\MigrationCreator;

class MigrationCreatorSoftelebyte extends MigrationCreator
{
    public function stubPath()
    {
        return __DIR__ . '/../stubs';
    }
}