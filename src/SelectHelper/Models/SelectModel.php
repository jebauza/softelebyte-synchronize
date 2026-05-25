<?php

namespace Softelebyte\SelectHelper\Models;


use Softelebyte\Builder\Models\Model;
use Softelebyte\SelectHelper\Models\Contracts\ModelContract;

abstract class SelectModel implements ModelContract
{
    private Model $model;
    protected SelectPostgresHelper $select;
    private string $completeName;

    public function __construct()
    {
        $this->model = $this->model();
        $this->completeName = $this->model->getCompleteTableName();
        $this->select = new SelectPostgresHelper();
    }

    public function completeName(): string
    {
        return $this->completeName;
    }

    public function sum(string $column, ?string $alias = null, bool $raw = true)
    {
        return $this->select->sum(
            $this->completeName . '.' . $column,
            $alias,
            $raw,
        );
    }

    public function avg(string $column, ?string $alias = null, bool $raw = true)
    {
        return $this->select->avg(
            $this->completeName . '.' . $column,
            $alias,
            $raw,
        );
    }

    public function monthYear(string $column, ?string $alias = null, bool $raw = true)
    {
        return $this->select->monthYear(
            $this->completeName . '.' . $column,
            $alias,
            $raw,
        );
    }


    protected function column(string $column, ?string $alias = null, bool $raw = true)
    {
        return $this->select->alias(
            $this->completeName . '.' . $column,
            $alias,
            $raw,
        );
    }
}
