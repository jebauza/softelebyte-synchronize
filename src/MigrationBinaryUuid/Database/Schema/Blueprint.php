<?php

namespace Softelebyte\MigrationBinaryUuid\Database\Schema;


use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;

class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    public function foreignRealBinary($column, $length = 16): ForeignIdColumnDefinition
    {
        return $this->columns[] = new ForeignIdColumnDefinition($this, [
            'type' => 'realBinary',
            'name' => $column,
            'length' => $length
        ]);
    }

    public function foreignRealUuid($column, $length = 16): ForeignIdColumnDefinition
    {
        return $this->columns[] = new ForeignIdColumnDefinition($this, [
            'type' => 'realUuid',
            'name' => $column,
            'length' => $length
        ]);
    }

    public function realBinary($column, $length = 16): ColumnDefinition
    {
        return $this->addColumn('realBinary', $column, compact('length'));
    }

    public function realUuid($column): ColumnDefinition
    {
        return $this->addColumn('realUuid', $column);
    }

    public function createdAt(): ColumnDefinition
    {
        return $this->addColumn('createdAt', 'created_at');
    }

    public function updatedAt(): ColumnDefinition
    {
        return $this->addColumn('updatedAt', 'updated_at');
    }

}