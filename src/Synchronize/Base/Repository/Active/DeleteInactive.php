<?php


namespace Softelebyte\Synchronize\Base\Repository\Active;


use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteInactiveContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;

class DeleteInactive implements DeleteInactiveContract
{
    protected BaseEtlModel $targetModel;
    protected SynchronizeValueObject $object;

    public function __construct(SynchronizeValueObject $object)
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
    }

    public function delete(): void
    {
        $days = config('synchronize.active_table_etl_minimum_days');
        DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable())
            ->where($this->targetModel::activeEtl(), '=', 0)
            ->where($this->targetModel::UPDATED_AT, '<=', CarbonImmutable::Now()->subDays($days))
            ->delete();
    }

    public function rollback(): void
    {
        $updatedAt = DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable())
            ->max($this->targetModel::UPDATED_AT);

        DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable())
            ->where($this->targetModel::activeEtl(), '=', 0)
            ->where($this->targetModel::UPDATED_AT, '=', $updatedAt)
            ->update(array($this->targetModel::activeEtl() => 1));
    }
}