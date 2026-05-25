<?php


namespace Softelebyte\Synchronize\Base\Repository\InsertAndUpdate;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertAndUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\NotPrimaryQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\PrimaryQuery;
use Softelebyte\Synchronize\Base\Models\Model;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupValueObject;

class GroupInsert implements InsertAndUpdateContract
{
    protected Model $targetModel;
    protected Model $originModel;
    protected GroupValueObject $object;

    public function __construct(GroupValueObject $object)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->originModel = resolve($object->originModel());
    }

    public function insert(): void
    {
        $queryOrigin = $this->principalQuery();

        $queryDestiny = DB::query()->fromSub($queryOrigin, $this->object->originName());
        if ($this->object instanceof NotPrimaryQuery) {
            $this->object->notPrimaryQuery($queryDestiny);
        }
        $queryDestiny = $queryDestiny->select($this->object->selectInsert());
        $queryDestiny->leftJoin(
            $this->targetModel->getCompleteTableName() . ' as ' . $this->object->targetName(),
            $this->object->on()
        )->whereNull($this->object->targetKey());
        DB::table($this->targetModel->getCompleteTableName())
            ->insertUsing(
                $this->object->insert(),
                $queryDestiny
            );
    }

    protected function principalQuery(): Builder
    {
        $queryOrigin = DB::table($this->originModel->getCompleteTableName(), $this->object->originName())
            ->select($this->object->select());
        if ($this->object instanceof PrimaryQuery) {
            $this->object->primaryQuery($queryOrigin);
        }
        return $queryOrigin;
    }

    public function update(): void
    {
        $queryOrigin = $this->principalQuery();

        $queryDestiny = DB::table($this->targetModel->getCompleteTableName(), $this->object->targetName())
            ->joinSub($queryOrigin, $this->object->originName(), $this->object->on());
        if ($this->object instanceof NotPrimaryQuery) {
            $this->object->notPrimaryQuery($queryDestiny);
        }
        $queryDestiny->update($this->object->updateSet());
    }

    public function getOriginCount(): int
    {
        $queryOrigin = $this->principalQuery();
        $query = DB::query()->fromSub($queryOrigin, $this->object->originName());
        return $query->count();
    }
}