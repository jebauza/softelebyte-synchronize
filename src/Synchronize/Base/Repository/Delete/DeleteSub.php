<?php


namespace Softelebyte\Synchronize\Base\Repository\Delete;


use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\Delete\SqlServer\DeleteSubContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Models\Model;

class DeleteSub implements DeleteContract
{
    protected Model $targetModel;
    protected Model $originModel;
    protected DeleteSubContract $object;

    public function __construct(DeleteSubContract $object)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
    }

    public function delete(): void
    {
        $query = DB::table($this->object->subFromQuery(), $this->object->originName())
            ->select($this->object->originKey());

        $destinyQuery = DB::table($this->targetModel->getCompleteTableName(), $this->object->targetName())
            ->leftJoinSub($query, $this->object->originName(), $this->object->on());
        if ($this->object instanceof InactiveQuery) {
            $destinyQuery = $this->object->inactiveQuery($destinyQuery);
        }
        $destinyQuery->whereNull($this->object->originKey());

        $destinyQuery->delete();
    }
}