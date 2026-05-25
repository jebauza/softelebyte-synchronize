<?php


namespace Softelebyte\Synchronize\Base\Repository\Mysql;


use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;
use Traversable;
use TypeError;
use Softelebyte\Synchronize\Base\Contracts\Repo\Data\Chunk;
use Softelebyte\Synchronize\Base\Contracts\Repo\Data\Repo;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\RepoValueObjectContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TargetModelKeyName;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;

class InsertUpdateFromCollection implements InsertUpdateContract
{
    protected int $dataCount;
    protected RepoValueObjectContract $object;
    protected Repo $repo;

    public function __construct(RepoValueObjectContract $object)
    {
        $this->object = $object;
        $this->repo = $this->object->repo();
        $this->dataCount = 0;
    }

    public function insertUpdate(): void
    {
        $data = $this->repo->getAll();
        $this->insertCollection($data);
    }

    protected function insertCollection(IteratorAggregate $data): void
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

        $i = 0;
        $array = [];
        $iterator = $data->getIterator();

        if (isset($iterator)) {
            $iterator->rewind();
            $end = $iterator->valid();
            if ($end) {
                $this->setArrayItem($iterator, $i, $array);
                $this->dataCount++;
                $limit = floor(config('synchronize.mysql_placeholder_max') / sizeof($array[0]));
                if ($this->repo instanceof Chunk) {
                    $limit = $this->repo->chunk();
                }
            }
            while ($end) {
                $end = $iterator->valid();
                while ($end && $i < $limit) {
                    $this->setArrayItem($iterator, $i, $array);
                    $end = $iterator->valid();
                    $this->dataCount++;
                }

                $targetModel::insertOnDuplicateKey(
                    $array,
                    $this->object->UpdateColumns(),
                    $keyName
                );
                $i = 0;
                $array = [];
            }
        }
    }

    protected function setArrayItem(Traversable $iterator, int &$i, array &$array): void
    {
        $row = $iterator->current();
        if ($row instanceof Arrayable) {
            $row = $row->toArray();
        }
        $array[$i++] = $row;
        $iterator->next();
    }

    public function getOriginCount(): int
    {
        return $this->dataCount;
    }
}