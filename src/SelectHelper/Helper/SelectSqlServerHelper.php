<?php


namespace Softelebyte\SelectHelper\Helper;


class SelectSqlServerHelper extends SelectHelper
{

    public function if($condition, $ifTrue, $ifFalse, $alias = null, $raw = true)
    {
        $sql = 'iif(' . $condition . ',' . $ifTrue . ',' . $ifFalse . ')';
        return $this->alias($sql, $alias, $raw);
    }
}
