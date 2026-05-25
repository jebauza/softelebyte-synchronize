<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\MultiRow;


use Illuminate\Support\Facades\DB;
use TypeError;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\ParentToChild;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;
use Softelebyte\Synchronize\Base\ValueObjects\Service\MultiParentRepoValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\MultiSimpleRepoValueObject;

class ChildValueObject
{
    protected MultiSimpleRepoValueObject $multiSimpleRepoValueObject;
    protected BaseEtlModel $baseEtlModel;

    public function __construct(string $baseObjectClass)
    {
        $baseObject = new $baseObjectClass();
        if (!$baseObject instanceof MultiSimpleRepoValueObject) {
            throw new TypeError('BaseObject must be an instance of ' . MultiSimpleRepoValueObject::class .
                ', ' . $baseObjectClass . ' given.');
        }
        $this->multiSimpleRepoValueObject = $baseObject;

        $baseModel = resolve($baseObject->targetModel());
        if (!$baseModel instanceof BaseEtlModel) {
            throw new TypeError('TargetModel must be an instance of ' . BaseEtlModel::class . ', ' .
                $baseObject->targetModel() . ' given.');
        }

        $this->baseEtlModel = $baseModel;
    }

    public function getMultiSimpleRepoValueObject(): MultiSimpleRepoValueObject
    {
        return $this->multiSimpleRepoValueObject;
    }

    public function getBaseEtlModel(): BaseEtlModel
    {
        return $this->baseEtlModel;
    }

    public function makeQuery(MultiParentRepoValueObject $object)
    {
        $query = DB::table($this->baseEtlModel->getCompleteTableName());

        if ($this->multiSimpleRepoValueObject instanceof ParentToChild) {
            $this->multiSimpleRepoValueObject->parentToChild($object);
        }

        if ($this->multiSimpleRepoValueObject instanceof InactiveQuery) {
            $query = $this->multiSimpleRepoValueObject->inactiveQuery($query);
        }

        return $query;
    }
}