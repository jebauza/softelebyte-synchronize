<?php


namespace Softelebyte\Synchronize\Base\Repository\Delete;


use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteContract;
use Softelebyte\Synchronize\Base\Contracts\Repo\OriginKeyAlias;
use Softelebyte\Synchronize\Base\Contracts\Repo\SelectOriginKeys;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Delete\SqlServer\DeleteValueObjectContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\PrimaryQuery;
use Softelebyte\Synchronize\Base\Models\Model;

class Delete implements DeleteContract
{
    protected Model $targetModel;
    protected Model $originModel;
    protected DeleteValueObjectContract $object;
    protected SelectOriginKeys $selectOriginKeys;

    public function __construct(DeleteValueObjectContract $object, SelectOriginKeys $selectOriginKeys)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->originModel = resolve($object->originModel());
        $this->selectOriginKeys = $selectOriginKeys;
    }

    public function delete(): void
    {
        $query = DB::table($this->originModel->getCompleteTableName(), $this->object->originName())
            ->select($this->selectOriginKeys->processKeys($this->object));
        if ($this->object instanceof PrimaryQuery) {
            $this->object->primaryQuery($query);
        }

        $destinyQuery = DB::table($this->targetModel->getCompleteTableName(), $this->object->targetName())
            ->leftJoinSub($query, $this->object->originName(), $this->object->on());
        if ($this->object instanceof InactiveQuery) {
            $destinyQuery = $this->object->inactiveQuery($destinyQuery);
        }
        $destinyQuery->whereNull($this->whereNullOriginKeys());
        $destinyQuery->delete();
    }

    protected function whereNullOriginKeys(): array
    {
        $originKeys = $this->object->originKey();
        if ($this->object instanceof OriginKeyAlias) {
            $originKeyAlias = $this->object->originKeyAlias();
            foreach ($originKeys as $i => $originKey) {
                $originKeys[$i] = $this->object->originName() . '.' . $originKeyAlias[$i];
            }
        }
        return $originKeys;
    }
}