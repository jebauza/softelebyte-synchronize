<?php


namespace Softelebyte\Synchronize\Base\Repository\Active;


use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteInactiveContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\ParentToChild;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;
use Softelebyte\Synchronize\Base\ValueObjects\MultiRow\ChildValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\MultiParentRepoValueObject;

class DeleteMultiInactive implements DeleteInactiveContract
{
    protected BaseEtlModel $targetModel;
    protected MultiParentRepoValueObject $object;

    public function __construct(MultiParentRepoValueObject $object)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
    }

    public function inactive(): void
    {
        $query = DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable());

        if ($this->object instanceof InactiveQuery) {
            $query = $this->object->inactiveQuery($query);
        }

        $query->update(array($this->targetModel::activeEtl() => 0));

        $baseObjects = $this->object->BaseObjects();

        foreach ($baseObjects as $baseObjectClass) {
            $child = new ChildValueObject($baseObjectClass);
            $baseModel = $child->getBaseEtlModel();
            $query = $child->makeQuery($this->object);

            $query->update(array($baseModel->getCompleteTableName() . '.' . $baseModel::activeEtl() => 0));
        }
    }

    public function delete(): void
    {
        $days = config('synchronize.active_table_etl_minimum_days');
        DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable())
            ->where($this->targetModel::activeEtl(), '=', 0)
            ->where($this->targetModel::UPDATED_AT, '<=', CarbonImmutable::Now()->subDays($days))
            ->delete();

        $baseObjects = $this->object->BaseObjects();

        foreach ($baseObjects as $baseObjectClass) {
            $child = new ChildValueObject($baseObjectClass);
            $baseObject = $child->getMultiSimpleRepoValueObject();

            if ($baseObject instanceof ParentToChild) {
                $baseObject->parentToChild($this->object);
            }
            $baseModel = $child->getBaseEtlModel();

            DB::table($baseModel->getCompleteTableName())
                ->where($baseModel::activeEtl(), '=', 0)
                ->where($baseModel::UPDATED_AT, '<=', CarbonImmutable::Now()->subDays($days))
                ->delete();
        }
    }

    public function rollback(): void
    {
        $updatedAt = DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable())
            ->max($this->targetModel::UPDATED_AT);

        DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable())
            ->where($this->targetModel::activeEtl(), '=', 0)
            ->where($this->targetModel::UPDATED_AT, '=', $updatedAt)
            ->update(array($this->targetModel::activeEtl() => 1));

        $baseObjects = $this->object->BaseObjects();

        foreach ($baseObjects as $baseObjectClass) {
            $child = new ChildValueObject($baseObjectClass);
            $baseObject = $child->getMultiSimpleRepoValueObject();

            if ($baseObject instanceof ParentToChild) {
                $baseObject->parentToChild($this->object);
            }

            $baseModel = $child->getBaseEtlModel();

            $updatedAt = DB::table($baseModel->getCompleteTableName())->max($this->targetModel::UPDATED_AT);

            DB::table($baseModel->getCompleteTableName())
                ->where($this->targetModel::activeEtl(), '=', 0)
                ->where($this->targetModel::UPDATED_AT, '=', $updatedAt)
                ->update(array($this->targetModel::activeEtl() => 1));
        }
    }
}
