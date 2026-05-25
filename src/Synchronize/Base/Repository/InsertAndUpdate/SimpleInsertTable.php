<?php


namespace Softelebyte\Synchronize\Base\Repository\InsertAndUpdate;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertAndUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Repo\SelectOriginKeys;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\NotPrimaryQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\PrimaryQuery;
use Softelebyte\Synchronize\Base\Models\Model;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\SimpleTableValueObject;

class SimpleInsertTable implements InsertAndUpdateContract
{
    protected Model $targetModel;
    protected Model $originModel;
    protected SimpleTableValueObject $object;
    protected SelectOriginKeys $selectOriginKeys;

    public function __construct(SimpleTableValueObject $object, SelectOriginKeys $selectOriginKeys)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->originModel = resolve($object->originModel());
        $this->selectOriginKeys = $selectOriginKeys;
    }

    public function insert(): void
    {
        $query = $this->principalQuery()
            ->select($this->object->select());
        if ($this->object instanceof NotPrimaryQuery) {
            $this->object->notPrimaryQuery($query);
        }
        $query->leftJoin(
            $this->targetModel->getCompleteTableName() . ' as ' . $this->object->targetName(),
            $this->object->on()
        )->whereNull($this->object->targetKey());
        DB::table($this->targetModel->getCompleteTableName())
            ->insertUsing(
                $this->object->insert(),
                $query
            );
    }

    protected function principalQuery(): Builder
    {
        $queryOrigin = DB::table($this->originModel->getCompleteTableName(), $this->object->originName())
            ->select($this->object->select());
        if ($this->object instanceof PrimaryQuery) {
            $queryOrigin = $this->object->primaryQuery($queryOrigin);
        }
        return $queryOrigin;
    }

    public function update(): void
    {
        $query = DB::table($this->targetModel->getCompleteTableName(), $this->object->targetName())
            ->join($this->originModel->getCompleteTableName() . ' as ' . $this->object->originName(), $this->object->on());
        if ($this->object instanceof PrimaryQuery) {
            $query = $this->object->primaryQuery($query);
        }
        if ($this->object instanceof NotPrimaryQuery) {
            $query = $this->object->notPrimaryQuery($query);
        }
        $query->update($this->object->updateSet());
    }

    public function getOriginCount(): int
    {
        $queryOrigin = $this->principalQuery()
            ->select($this->selectOriginKeys->processKeys($this->object));
        $query = DB::query()->fromSub($queryOrigin, $this->object->originName());
        return $query->count();
    }
}