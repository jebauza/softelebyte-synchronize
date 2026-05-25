<?php


namespace Softelebyte\Synchronize\Base\Services\Table;


use Illuminate\Database\Eloquent\Model;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;

class ServiceNotDeleteTable implements ServiceContract
{
    protected Model $targetModel;
    protected SynchronizeValueObject $object;
    protected InsertUpdateContract $insertUpdateContract;

    public function __construct(SynchronizeValueObject $object, InsertUpdateContract $insertUpdateContract)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->insertUpdateContract = $insertUpdateContract;
    }

    public function handle(): void
    {
        $this->insertUpdateContract->insertUpdate();
    }

    public function getIsSameCount(): bool
    {
        /*$query = $this->targetModel::query()->toBase();
        $countDestiny = $query->count();
        $countOrigin = $this->insertUpdateContract->getOriginCount();
        return $countOrigin == $countDestiny;*/
        //TODO: Añadir la cuenta
        return true;
    }
}