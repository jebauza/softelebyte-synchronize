<?php


namespace Softelebyte\Synchronize\Base\Helper\SqlServer;

use Exception;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DropAndCreateForeignAndKey
{
    protected Collection $processedGroupedKeys;
    protected Collection $processedForeignKeys;

    protected string $targetTable;

    public function __construct(string $targetTable)
    {
        $this->processedGroupedKeys = new Collection();
        $this->processedForeignKeys = new Collection();
        $this->targetTable = $targetTable;
    }

    public function setTargetTable(string $targetTable): void
    {
        $this->targetTable = $targetTable;
    }

    public function dropIndex(): void
    {
        try {
            $keys =
                DB::table('sys.tables as t')
                    ->select([
                        't.name as table_name',
                        'i.name as index_name',
                        'i.type_desc',
                        'i.is_unique',
                        'i.is_primary_key',
                        'i.is_padded',
                        'i.ignore_dup_key',
                        'i.allow_row_locks',
                        'i.allow_page_locks',
                        'c.name as column_name',
                        'ic.is_descending_key'
                    ])
                    ->join('sys.indexes as i', 't.object_id', '=', 'i.object_id')
                    ->join('sys.index_columns as ic', function (JoinClause $join) {
                        $join->on('i.object_id', '=', 'ic.object_id')
                            ->on('i.index_id', '=', 'ic.index_id');
                    })
                    ->join('sys.columns as c', function (JoinClause $join) {
                        $join->on('ic.object_id', '=', 'c.object_id')
                            ->on('ic.column_id', '=', 'c.column_id');
                    })
                    ->where('t.name', '=', $this->targetTable)
                    ->orderBy('ic.key_ordinal')
                    ->get();

            if ($keys->count() == 0) {
                return;
            }

            $grouped_keys = $keys->groupBy('index_name');

            $sql = 'BEGIN TRANSACTION;' . PHP_EOL;

            foreach ($grouped_keys as $index => $grouped_key) {
                if ($grouped_key->first()->is_primary_key) {
                    $sql .= 'ALTER TABLE ' . $this->targetTable . ' DROP CONSTRAINT ' . $index . ';' . PHP_EOL;
                } else {
                    $sql .= 'DROP INDEX ' . $index . ' ON ' . $this->targetTable . ';' . PHP_EOL;
                }
            }

            $sql .= 'COMMIT;';

            DB::statement($sql);
        } catch (Exception $exception) {
            DB::statement('ROLLBACK');
            throw $exception;
        }
        $this->processedGroupedKeys = $grouped_keys;
    }

    public function createIndex(): void
    {
        if ($this->processedGroupedKeys->count() == 0) {
            return;
        }

        $switchString[0] = 'OFF';
        $switchString[1] = 'ON';

        $sql = '';
        foreach ($this->processedGroupedKeys as $index => $grouped_key) {

            $first = $grouped_key->first();
            if ($first->is_primary_key) {
                $sql .= 'ALTER TABLE ' . $this->targetTable . ' ADD CONSTRAINT ' . $index . ' PRIMARY KEY ' . $first->type_desc . PHP_EOL;
            } else {
                $sql .= 'CREATE ' . ($first->is_unique ? 'UNIQUE ' : '') . $first->type_desc . ' INDEX ' . $index . ' ON ' .
                    $this->targetTable . PHP_EOL;
            }

            $sql .= '(';
            foreach ($grouped_key as $column) {
                $sql .= PHP_EOL . $column->column_name . ' ' . ($column->type_desc ? 'DESC ' : 'ASC') . ',';
            }
            $sql = substr($sql, 0, -1);
            $sql .= PHP_EOL . ')WITH (' .
                'PAD_INDEX = ' . $switchString[$first->is_padded] . ', ' .
                'IGNORE_DUP_KEY = ' . $switchString[$first->ignore_dup_key] . ', ' .
                'ALLOW_ROW_LOCKS = ' . $switchString[$first->allow_row_locks] . ', ' .
                'ALLOW_PAGE_LOCKS = ' . $switchString[$first->allow_page_locks] . ' ' .
                ') ON [PRIMARY];' . PHP_EOL;
        }

        DB::statement($sql);
    }

    public function dropForeign(): void
    {
        try {
            $keys =
                DB::table('sys.foreign_keys as f')
                    ->select([
                        'f.name as foreign_name',
                        'f.delete_referential_action',
                        'f.delete_referential_action_desc',
                        'f.update_referential_action',
                        'f.update_referential_action_desc',
                        'pt.name as primary_table',
                        'pc.name as primary_column',
                        'rt.name as reference_table',
                        'rc.name as reference_column'
                    ])
                    ->join('sys.foreign_key_columns as fc', 'fc.constraint_object_id', '=', 'f.object_id')
                    ->join('sys.columns as pc', function (JoinClause $join) {
                        $join->on('pc.object_id', '=', 'fc.parent_object_id')
                            ->on('pc.column_id', '=', 'fc.parent_column_id');
                    })
                    ->join('sys.columns as rc', function (JoinClause $join) {
                        $join->on('rc.object_id', '=', 'fc.referenced_object_id')
                            ->on('rc.column_id', '=', 'fc.referenced_column_id');
                    })
                    ->join('sys.tables as pt', 'pt.object_id', '=', 'pc.object_id')
                    ->join('sys.tables as rt', 'rt.object_id', '=', 'rc.object_id')
                    ->where('pt.name', '=', 'sl_softelebyte_smu_ous')
                    ->orderBy('fc.constraint_column_id')
                    ->get();

            if ($keys->count() == 0) {
                return;
            }

            $grouped_keys = $keys->groupBy('foreign_name');

            $sql = 'BEGIN TRANSACTION;' . PHP_EOL;

            foreach ($grouped_keys as $index => $grouped_key) {
                $sql .= 'ALTER TABLE ' . $grouped_key->first()->primary_table . ' DROP CONSTRAINT ' . $index . ';' . PHP_EOL;
            }

            $sql .= 'COMMIT;';

            DB::statement($sql);
        } catch (Exception $exception) {
            DB::statement('ROLLBACK');
            throw $exception;
        }
        $this->processedForeignKeys = $grouped_keys;
    }

    public function createForeignKeys(): void
    {
        if ($this->processedForeignKeys->count() == 0) {
            return;
        }

        $sql = '';
        foreach ($this->processedForeignKeys as $index => $grouped_key) {
            $first = $grouped_key->first();
            $sql .= 'ALTER TABLE ' . $first->primary_table . ' WITH CHECK ADD CONSTRAINT ' . $index . ' FOREIGN KEY(';

            $reference = PHP_EOL . 'REFERENCES ' . $first->reference_table . ' (';
            foreach ($grouped_key as $column) {
                $sql .= $column->primary_column . ',';
                $reference .= $column->reference_column . ',';
            }
            $sql = substr($sql, 0, -1);
            $reference = substr($reference, 0, -1);
            $sql .= ')' . $reference . ')';
            if ($first->delete_referential_action != 0) {
                $sql .= PHP_EOL . ' ON UPDATE ' . $first->update_referential_action_desc;
            }
            if ($first->delete_referential_action != 0) {
                $sql .= PHP_EOL . ' ON DELETE' . $first->delete_referential_action_desc;
            }
            $sql .= ';' . PHP_EOL .
                'ALTER TABLE ' . $first->primary_table . ' CHECK CONSTRAINT ' . $index . ';' . PHP_EOL;
        }

        DB::statement($sql);
    }
}