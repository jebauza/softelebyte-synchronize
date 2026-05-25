<?php


namespace Softelebyte\Synchronize\Base\Repository\InsertAndUpdate;


use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertAndUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\AdditionalQueryChange;
use Softelebyte\Synchronize\Base\Models\Model;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupSubValueObject;

class FromSubInsertAndUpdate implements InsertAndUpdateContract
{
    protected Model $targetModel;
    protected GroupSubValueObject $object;

    public function __construct(GroupSubValueObject $object)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
    }

    public function insert(): void
    {
        $queryDestiny = $this->baseQuery();

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

    public function baseQuery()
    {
        $queryOrigin = DB::query()->fromSub($this->object->subFromQuery(), $this->object->originName());
        if ($this->object instanceof AdditionalQueryChange) {
            $this->object->additionalQueryChange($queryOrigin);
        }
        $queryOrigin->select($this->object->selectInsert());
        return $queryOrigin;
    }

    public function update(): void
    {
        $queryOrigin = $this->baseQuery();

        $queryDestiny = DB::table($this->targetModel->getCompleteTableName(), $this->object->targetName())
            ->joinSub($queryOrigin, $this->object->originName(), $this->object->on());
        $queryDestiny->update($this->object->updateSet());
    }

    public function getOriginCount(): int
    {
        $query = DB::query()->fromSub($this->baseQuery(), $this->object->originName());
        return $query->count();
    }
}