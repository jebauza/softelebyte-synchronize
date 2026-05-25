<?php


namespace Softelebyte\SelectHelper\Helper;


use Illuminate\Support\Facades\DB;
use Softelebyte\SelectHelper\ValueObjects\CaseValueObject;

abstract class SelectHelper
{
    public function raw($sql, $raw = true)
    {
        if (!$raw) {
            return $sql;
        }
        return DB::raw($sql);
    }

    public function ceroAs(?string $alias, $raw = true)
    {
        return $this->alias(0, $alias, $raw);
    }

    public function alias($sql, $alias, $raw = true)
    {
        if ($alias == '' || is_null($alias)) {
            return $this->raw($sql, $raw);
        }
        $sql = $sql . ' as ' . $alias;
        return $this->raw($sql, $raw);
    }

    abstract public function if($condition, $ifTrue, $ifFalse, $alias = null, $raw = true);

    public function sum($field, $alias = null, $raw = true)
    {
        $sql = 'sum(' . $field . ')';
        return $this->alias($sql, $alias, $raw);
    }

    public function count($field = '*', $alias = null, $raw = true)
    {
        $sql = 'count(' . $field . ')';
        return $this->alias($sql, $alias, $raw);
    }

    public function isNull($field)
    {
        return $field . ' is null';
    }

    public function dateDiff($first, $second)
    {
        return 'DATEDIFF(' . $first . ',' . $second . ')';
    }

    public function case(): CaseValueObject
    {
        return (new CaseValueObject());
    }

    public function max($field, $alias = null, $raw = true)
    {
        $sql = 'MAX(' . $field . ')';
        return $this->alias($sql, $alias, $raw);
    }

    public function countIf($condition, $alias = null, $raw = true, $ifTrue = 1, $ifFalse = 'null')
    {
        $sqlIf = $this->if($condition, $ifTrue, $ifFalse, null, false);
        return $this->count($sqlIf, $alias, $raw);
    }

    public function sumIf($condition, $columnToSum, $defaultSum = 0, $alias = null, $raw = true)
    {
        $sqlIf = $this->if($condition, $columnToSum, $defaultSum, null, false);
        return $this->sum($sqlIf, $alias, $raw);
    }

    public function avg($avg, $alias = null, $raw = true)
    {
        $sql = 'avg(' . $avg . ')';
        return $this->alias($sql, $alias, $raw);
    }

    public function divisionByZero($totalPercentage, $partialPercentage, $alias = null, $raw = true)
    {
        $condition = $totalPercentage . '!=0';
        $division = $partialPercentage . '/' . $totalPercentage;
        return $this->if($condition, $division, 0, $alias, $raw);
    }

    public function percentage($totalPercentage, $partialPercentage, $alias = null, $raw = true, $decimal = true)
    {
        $d = '100.0';
        if (!$decimal) {
            $d = '100';
        }
        $condition = $totalPercentage . '!=0';
        $percentage = '((' . $partialPercentage . '*' . $d . '/' . $totalPercentage . '))';
        return $this->if($condition, $percentage, 0, $alias, $raw);
    }

    public function currentDate($alias = 'date', $raw = true)
    {
        return $this->alias('CURRENT_DATE()', $alias, $raw);
    }
}
