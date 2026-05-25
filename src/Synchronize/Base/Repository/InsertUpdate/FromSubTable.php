<?php


namespace Softelebyte\Synchronize\Base\Repository\InsertUpdate;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\FromTable\SqlMaker;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\AdditionalQueryChange;
use Softelebyte\Synchronize\Base\ValueObjects\Service\FromSubTableValueObject;

class FromSubTable implements SqlMaker
{
    protected FromSubTableValueObject $object;

    public function __construct(FromSubTableValueObject $object)
    {
        $this->object = $object;
    }

    public function getPrincipalQuery(): Builder
    {
        $query = $this->object->subFromQuery();
        $fromSubQuery = DB::query()->fromSub(
            $query,
            $this->object->originName()
        )
            ->select($this->object->select());
        if ($this->object instanceof AdditionalQueryChange) {
            $fromSubQuery = $this->object->additionalQueryChange($fromSubQuery);
        }
        return $fromSubQuery;
    }
}