<?php


namespace Softelebyte\Synchronize\Base\Repository\Mysql;


use TypeError;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\ParentToChild;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TargetModelKeyName;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;
use Softelebyte\Synchronize\Base\ValueObjects\MultiRow\ChildValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\MultiParentRepoValueObject;

class InsertUpdateFromMultiParent implements InsertUpdateContract
{
    protected int $dataCount;
    protected MultiParentRepoValueObject $object;

    public function __construct(MultiParentRepoValueObject $object)
    {
        $this->object = $object;
        $this->dataCount = 0;
    }

    public function insertUpdate(): void
    {
        $keyName = null;
        if ($this->object instanceof TargetModelKeyName) {
            $keyName = $this->object->targetModelKeyName();
        }

        $targetModel = resolve($this->object->targetModel());
        if (!$targetModel instanceof BaseEtlModel) {
            throw new TypeError('TargetModel must be an instance of ' . BaseEtlModel::class . ', ' .
                $this->object->targetModel() . ' given.');
        }

        $repo = $this->object->repo();
        $data = $repo->getAll();
        $this->insertCollection($data->getPrincipal(), $targetModel, $this->object->UpdateColumns(), $keyName);

        $baseObjects = $this->object->BaseObjects();

        foreach ($baseObjects as $baseObjectClass) {
            $keyName = null;
            $child = new ChildValueObject($baseObjectClass);
            $baseObject = $child->getMultiSimpleRepoValueObject();

            if ($baseObject instanceof ParentToChild) {
                $baseObject->parentToChild($this->object);
            }

            if ($baseObject instanceof TargetModelKeyName) {
                $keyName = $baseObject->targetModelKeyName();
            }

            $baseModel = $child->getBaseEtlModel();

            $this->insertCollection(
                $data->{'get' . $baseObject->property()}(),
                $baseModel,
                $baseObject->updateColumns(),
                $keyName
            );
        }
    }

    protected function insertCollection(array $data, BaseEtlModel $model, array $columns, string $keyName = null): void
    {
        if (empty($data)) {
            return;
        }

        $chunkData = floor(config('synchronize.mysql_placeholder_max') / sizeof($data[0]));
        $chunks = array_chunk($data, $chunkData);

        foreach ($chunks as $chunk) {
            $this->dataCount += count($chunk);
            $model::insertOnDuplicateKey(
                $chunk,
                $columns,
                $keyName
            );
        }
    }

    public function getOriginCount(): int
    {
        return $this->dataCount;
    }
}
