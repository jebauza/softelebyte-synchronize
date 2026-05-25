<?php

namespace Softelebyte\SelectHelper\Models;

use Softelebyte\SelectHelper\Helper\SelectHelper;

class SelectPostgresHelper extends SelectHelper
{
    public function concat(array $concat, ?string $alias = null, $raw = true)
    {
        $sql = 'CONCAT(' . implode(',', $concat) . ')';
        return $this->alias($sql, $alias, $raw);
    }

    public function round(string $field, int $decimals = 2, ?string $alias = 'date', $raw = true)
    {
        $sql = 'round(' . $field . ',' . $decimals . ')';
        return $this->alias($sql, $alias, $raw);
    }

    public function nullAs(?string $alias, $raw = true)
    {
        $sql = '\'null\'';
        return $this->alias($sql, $alias, $raw);
    }

    public function dateYearMonth01(string $date, ?string $alias = 'date', $raw = true)
    {
        $sql = 'DATE_FORMAT(' . $date . ', "%Y-%m-01")';
        return $this->alias($sql, $alias, $raw);
    }

    public function date(string $date, ?string $alias = 'date', $raw = true)
    {
        $sql = 'DATE_FORMAT(' . $date . ', "%Y-%m-%d")';
        return $this->alias($sql, $alias, $raw);
    }

    public function if($condition, $ifTrue, $ifFalse, $alias = null, $raw = true)
    {
        $sql = '
        CASE
            WHEN ' . $condition . ' THEN ' . $ifTrue . '
            ELSE ' . $ifFalse . '
       END
       ';
        return $this->alias($sql, $alias, $raw);
    }
    public function monthYear($field, $alias = null, $raw = true)
    {
        $sql = 'to_char(' . $field . ',\'yyyy-mm-01\')';
        return $this->alias($sql, $alias, $raw);
    }
}
