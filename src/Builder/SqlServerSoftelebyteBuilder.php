<?php


namespace Softelebyte\Builder;


trait SqlServerSoftelebyteBuilder
{
    public function sqlSumOverPartition(string $column, array $partitionColumns = null, string $alias = null)
    {
        $partition = '';
        $alias = $alias ?? 'sum_' . $column;

        if(isset($partitionColumns)) {
            $partition .= 'PARTITION BY ' . implode(',', $partitionColumns);
        }
        
        $this->selectRaw(
            'SUM(' . $column . ') OVER (' . $partition . ') as ' . $alias
        );
        return $this;
    }


    public function filterByCompositeKey(array $columns, array $values, $delimiter = '_')
    {
        $this->where(function ($queryValues) use ($columns, $values, $delimiter) {
            foreach ($values as $value) {
                $queryValues->orWhere(function ($queryValue) use ($columns, $value, $delimiter) {
                    $compositeValue = explode($delimiter, $value);
                    $this->checkCompositeKeyIsValid($columns, $compositeValue);
                    foreach ($columns as $key => $column) {
                        $queryValue->where($column, $compositeValue[$key]);
                    }
                });
            }
        });
    }

    public function checkCompositeKeyIsValid($columns, $value)
    {
        if (count($columns) != count($value)) {
            abort(403, 'No coincide el número de columnas');
        }
        return true;
    }
}
