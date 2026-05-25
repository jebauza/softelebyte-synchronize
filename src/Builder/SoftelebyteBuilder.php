<?php


namespace Softelebyte\Builder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Softelebyte\SoftelebyteJoins\SoftelebyteJoins;

class SoftelebyteBuilder extends Builder
{
    use SoftelebyteJoins;
    use SqlServerSoftelebyteBuilder;
    use InsertOnDuplicateSoftelebyteBuilder;

    public function selectYear($column, string $alias = null): self
    {
        $yearGrammar = 'Year(';

        if($this->query->grammar instanceof PostgresGrammar) {
            $yearGrammar = 'date_part(\'year\',';
        }

        $columnName = last(explode('.',$column));

        $this->selectRaw(
            $yearGrammar . $this->query->grammar->wrap($column) . ') as ' .
            ($alias ?? $this->query->grammar->wrap('Year_' . $columnName))
        );
        return $this;
    }

    public function selectMonth($column, string $alias = null): self
    {
        $monthGrammar = 'Month(';

        if($this->query->grammar instanceof PostgresGrammar) {
            $monthGrammar = 'date_part(\'month\',';
        }

        $columnName = last(explode('.',$column));

        $this->selectRaw(
            $monthGrammar . $this->query->grammar->wrap($column) . ') as ' .
            $alias ?? $this->query->grammar->wrap('Month_' . $wrapColumn)
        );
        return $this;
    }

    public function whereUuid($column, $operator = null, $value = null, $boolean = 'and')
    {
        $valueCast = $value;
        if($this->getConnection() instanceof MySqlConnection) {
            $valueCast = str_replace('-', '', $value);
            $valueCast = hex2bin($valueCast);
        }
        $this->where($column, $operator, $valueCast, $boolean);
        return $this;
    }

    public function whereUuidWithKey($uuid, $uuidColumn = null): self
    {
        $uuidColumn = ! is_null($uuidColumn) && in_array($uuidColumn, $this->model->getKeyName())
            ? $uuidColumn
            : $this->model->getKeyName();

        $uuid = Uuid::fromString($uuid)->getBytes();

        $this->whereIn($uuidColumn, Arr::wrap($uuid));
        return $this;
    }

    public function whereLike($column, $value = null, $boolean = 'and'): self
    {
        $this->where($column, 'like', hex2bin($value), $boolean);
        return $this;
    }

    public function whereNotLike($column, $value = null, $boolean = 'and'): self
    {
        $this->where($column, 'not like', $value, $boolean);
        return $this;
    }

    public function orderByCount($direction = null): self
    {
        $this->orderBy(DB::raw('count(*)'), $direction);
        return $this;
    }

    public function groupByYear($column): self
    {
        $yearGrammar = 'Year(';

        if($this->query->grammar instanceof PostgresGrammar) {
            $yearGrammar = 'date_part(\'year\',';
        }

        $this->groupByRaw($yearGrammar . $this->query->grammar->wrap($column) . ')');
        return $this;
    }

    public function groupByMonth($column): self
    {
        $monthGrammar = 'Month(';

        if($this->query->grammar instanceof PostgresGrammar) {
            $monthGrammar = 'date_part(\'month\',';
        }

        $this->groupByRaw($monthGrammar . $this->query->grammar->wrap($column) . ')');
        return $this;
    }

    public function orderByYear($column): self
    {
        $yearGrammar = 'Year(';

        if($this->query->grammar instanceof PostgresGrammar) {
            $yearGrammar = 'date_part(\'year\',';
        }

        $this->orderByRaw($yearGrammar . $this->query->grammar->wrap($column) . ')');
        return $this;
    }

    public function orderByMonth($column): self
    {
        $monthGrammar = 'Month(';

        if($this->query->grammar instanceof PostgresGrammar) {
            $monthGrammar = 'date_part(\'month\',';
        }

        $this->orderByRaw($monthGrammar . $this->query->grammar->wrap($column) . ')');
        return $this;
    }
}
