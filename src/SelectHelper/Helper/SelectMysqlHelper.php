<?php


namespace Softelebyte\SelectHelper\Helper;


class SelectMysqlHelper extends SelectHelper
{
    public function if($condition, $ifTrue, $ifFalse, $alias = null, $raw = true)
    {
        $sql = 'if(' . $condition . ',' . $ifTrue . ',' . $ifFalse . ')';
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
}
