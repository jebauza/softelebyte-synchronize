<?php


namespace Softelebyte\Synchronize\Base\Services\Table;


use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\Builder;
use Softelebyte\Builder\SoftelebyteBuilder;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteInactiveContract;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;

class ServiceTable implements ServiceContract
{
    protected BaseEtlModel $targetModel;
    protected SynchronizeValueObject $object;
    protected DeleteInactiveContract $delete;
    protected InsertUpdateContract $insertUpdateContract;

    public function __construct(SynchronizeValueObject $object, DeleteInactiveContract $delete, InsertUpdateContract $insertUpdateContract)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->delete = $delete;
        $this->insertUpdateContract = $insertUpdateContract;
    }

    public function handle(): void
    {
        $this->delete->inactive();
        try {
            $this->insertUpdateContract->insertUpdate();
            $this->delete->delete();
        } catch (\Exception $exception) {
            $this->delete->rollback();
            throw $exception;
        }
    }

    public function getIsSameCount(): bool
    {
        $query = $this->targetModel::query();
        $query = $query->toBase();
        $query = $this->applyInactiveQuery($query);
        $countDestiny = $query->count();
        $countOrigin = $this->insertUpdateContract->getOriginCount();
        return $countOrigin == $countDestiny;
    }

    protected function applyInactiveQuery(Builder $query): Builder
    {
        if ($this->object instanceof InactiveQuery) {
            $query = $this->object->inactiveQuery($query);
        }
        return $query;
    }
}