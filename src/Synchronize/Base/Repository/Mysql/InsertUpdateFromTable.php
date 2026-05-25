<?php


namespace Softelebyte\Synchronize\Base\Repository\Mysql;


use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\DB;
use TypeError;
use Softelebyte\Builder\SoftelebyteBuilder;
use Softelebyte\Synchronize\Base\Contracts\Repo\FromTable\SqlMaker;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\FromTableObject;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TargetModelKeyName;

class InsertUpdateFromTable implements InsertUpdateContract
{
    protected FromTableObject $object;
    protected SqlMaker $sqlMaker;

    public function __construct(FromTableObject $object, SqlMaker $sqlMaker)
    {
        $this->object = $object;
        $this->sqlMaker = $sqlMaker;
    }

    public function insertUpdate(): void
    {
        $keyName = null;
        if ($this->object instanceof TargetModelKeyName) {
            $keyName = $this->object->targetModelKeyName();
        }

        $subQuery = $this->sqlMaker->getPrincipalQuery();
        $targetModel = resolve($this->object->targetModel());
        $query = $targetModel::query();
        if (!$query instanceof SoftelebyteBuilder) {
            throw new TypeError('Target model must have SoftelebyteBuilder for use insertUsingOnDuplicate');
        }
        $query
            ->insertUsingOnDuplicate(
                $this->object->selectInsert(),
                $subQuery,
                $this->object->UpdateColumns(),
                $keyName
            );
    }

    public function getOriginCount(): int
    {
        $query = $this->sqlMaker->getPrincipalQuery();
        $targetModel = resolve($this->object->targetModel());
        $connection = $targetModel->getConnectionName();

        if($query->getGrammar() instanceof PostgresGrammar) {
            return DB::connection($connection)->query()->fromSub(
                $query,
                'origin'
            )->count();
        }

        $sql = $query->toSql();
        $sql = preg_replace('/^(select )/i', '$1SQL_CALC_FOUND_ROWS ', $sql);
        DB::connection($connection)->select($sql, $query->getBindings());
        return DB::connection($connection)->selectOne('select FOUND_ROWS() as count;')->count;
    }
}