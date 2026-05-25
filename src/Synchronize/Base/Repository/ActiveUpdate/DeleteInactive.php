<?php


namespace Softelebyte\Synchronize\Base\Repository\ActiveUpdate;


use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteInactiveContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;
use Softelebyte\Synchronize\Base\Models\BaseModel;

class DeleteInactive implements DeleteInactiveContract
{
    protected BaseModel $targetModel;
    protected SynchronizeValueObject $object;
    protected Builder $inactiveBuilder;

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

        $this->inactiveBuilder = clone $query;

        $query->where($this->targetModel->getTable() . '.' . $this->targetModel::activeBe(), '=', 1)
            ->update(array($this->targetModel->getTable() . '.' . $this->targetModel::activeEtl() => 0));
    }

    public function delete(): void
    {
        $this->inactiveUpdate();
        $this->deleteUpdate();
    }

    protected function inactiveUpdate(): void
    {
        $this->inactiveBuilder
            ->where($this->targetModel->getTable() . '.' . $this->targetModel::activeEtl(), '=', 0)
            ->update([$this->targetModel->getTable() . '.' . $this->targetModel::activeBe() => 0]);
    }

    protected function deleteUpdate(): void
    {
        $days = config('synchronize.active_table_minimum_days');
        DB::connection($this->targetModel->getConnectionName())->table($this->targetModel->getTable())
            ->where($this->targetModel::activeBe(), '=', 0)
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
            ->update([$this->targetModel::activeBe() => 1, $this->targetModel::activeEtl() => 1]);
    }
}
