<?php

namespace Softelebyte\QueryFilters\Concerns;

trait AvoidDuplicateJoin
{
    private $appliedJoins = [];

    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $second ??= $operator;
        $fields = [$first, $second];

        $key = $this->generateKey($table, $fields);
        
        if (in_array($key, $this->appliedJoins)) {
            return $this;
        }

        $this->appliedJoins[] = $key;

        return parent::join($table, $first, $operator, $second, $type, $where);
    }

    private function generateKey(string $table, array $fields): string
    {
        sort($fields);

        return implode('|', [...[$table], ...$fields]);
    }
}
