<?php


namespace Softelebyte\Builder;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Facades\DB;

trait InsertOnDuplicateSoftelebyteBuilder
{
    /**
     * @param array $insertColumns
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param array|null $onDuplicateKeyColumns
     * @param string|null $keyNameDuplicate
     * @return int
     */
    public function insertUsingOnDuplicate(array $insertColumns, $query, array $onDuplicateKeyColumns = null, string $keyNameDuplicate = null): int
    {
        $sql = $query->toSql();

        $this->addBinding($query->getBindings());

        $insert = $this->query->grammar->compileInsertUsing($this->query, $insertColumns, $sql);

        return $this->addDuplicateAndExecute($onDuplicateKeyColumns, $insertColumns, $insert, $keyNameDuplicate);
    }

    /**
     * @param array $data
     * @param array|null $onDuplicateKeyColumns
     * @param string|null $keyNameDuplicate
     * @return int
     */
    public function insertOnDuplicateKey(array $data, array $onDuplicateKeyColumns = null, string $keyNameDuplicate = null): int
    {
        if (empty($data)) {
            return false;
        }

        $this->query = DB::table($this->getCompleteTableName());
        $insert = $this->query->grammar->compileInsert($this->query,$data);

        $first = array_keys(reset($data));

        $binding = call_user_func_array('array_merge', array_map('array_values', $data));

        $this->addBinding($binding);

        return $this->addDuplicateAndExecute($onDuplicateKeyColumns, $first, $insert, $keyNameDuplicate);
    }

    protected function buildValuesList(array $updatedColumns)
    {
        $out = '';
        $valueColumn = '';

        foreach ($updatedColumns as $key => $value) {
            if (is_numeric($key)) {
                $column = $value;
                if($this->query->grammar instanceof MySqlGrammar) {
                    $valueColumn = ' VALUES(' . $this->query->grammar->wrap($value) . ') ';
                }
                if($this->query->grammar instanceof PostgresGrammar) {
                    $valueColumn = ' EXCLUDED.' . $this->query->grammar->wrap($value) . ' ';
                }
            } else {
                $column = $key;
                if ($value instanceof Expression) {
                    $valueColumn = $value->getValue($this->query->grammar);
                } else {
                    $valueColumn = '?';
                    $this->addBinding($value);
                }
            }

            $out .= $this->query->grammar->wrap($column) . ' = ' . $valueColumn . ',';
        }

        return substr($out, 0, -1);
    }

    protected function addDuplicateAndExecute(?array $onDuplicateKeyColumns, array $insertColumns, string $insert, ?string $keyNameDuplicate): int
    {
        $onDuplicateKeyColumns = $onDuplicateKeyColumns ?? $insertColumns;

        if(!isset($keyNameDuplicate)) {
            $model = $this->getModel();
            $table = $model->getTable();
            $keyNameDuplicate = $table . '_pkey';
        }

        if ($this->query->grammar instanceof MySqlGrammar) {
            $insert .= PHP_EOL . 'ON DUPLICATE KEY UPDATE ';
        }

        if ($this->query->grammar instanceof PostgresGrammar) {
            $insert .= PHP_EOL . 'ON CONFLICT ON CONSTRAINT ' . $keyNameDuplicate . ' DO UPDATE SET ';
        }

        $insert .= $this->buildValuesList($onDuplicateKeyColumns);

        return $this->query->connection->affectingStatement(
            $insert,
            $this->query->getBindings()
        );
    }

    public function getCompleteTableName(): string
    {
        $model = $this->getModel();

        $databaseConnection = $this->getConnection();
        $databaseSpace = '.';
        if ($databaseConnection instanceof SqlServerConnection) {
            $databaseSpace .= ($databaseConnection->getConfig('schema') ?? 'dbo' ) . '.';
        }

        if ($databaseConnection instanceof PostgresConnection) {
            $databaseSpace .= $databaseConnection->getConfig('schema') . '.';
        }

        $table = $model->getTable();

        return $databaseConnection->getDatabaseName() . $databaseSpace . $table;
    }
}
