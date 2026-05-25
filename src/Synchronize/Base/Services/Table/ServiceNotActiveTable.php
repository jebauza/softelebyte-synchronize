<?php


namespace Softelebyte\Synchronize\Base\Services\Table;


use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteContract;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertAndUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\ServiceNotDuplicateKey\BaseContract;
use Softelebyte\Synchronize\Base\Models\Model;

class ServiceNotActiveTable implements ServiceContract
{
    protected Model $targetModel;
    protected BaseContract $object;
    protected DeleteContract $delete;
    protected InsertAndUpdateContract $insertAndUpdateContract;

    public function __construct(BaseContract $object, DeleteContract $delete, InsertAndUpdateContract $insertUpdateContract)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->delete = $delete;
        $this->insertAndUpdateContract = $insertUpdateContract;
    }

    public function handle(): void
    {
        $this->delete->delete();
        $this->insertAndUpdateContract->update();
        $this->insertAndUpdateContract->insert();
    }

    public function getIsSameCount(): bool
    {
        $query = DB::table($this->targetModel->getCompleteTableName(),$this->object->targetName());
        if ($this->object instanceof InactiveQuery) {
            $query = $this->object->inactiveQuery($query);
        }
        $countDestiny = $query->count();
        $countOrigin = $this->insertAndUpdateContract->getOriginCount();
        return $countOrigin == $countDestiny;
    }
}