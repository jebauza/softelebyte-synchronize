<?php


namespace Softelebyte\Synchronize\Base\Repository\InsertUpdate;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\FromTable\SqlMaker;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\AdditionalQueryChange;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;
use Softelebyte\Synchronize\Base\ValueObjects\Service\SimpleTableValueObject;

class SimpleTable implements SqlMaker
{
    protected SimpleTableValueObject $object;

    public function __construct(SimpleTableValueObject $object)
    {
        $this->object = $object;
    }

    public function getPrincipalQuery(): Builder
    {
        $originModel = resolve($this->object->originModel());

        $query = DB::table($originModel->getCompleteTableName())
            ->select($this->object->select());

        if ($originModel instanceof BaseEtlModel) {
            $query->where($originModel->getTable() . '.' . $originModel::activeEtl(), '=', 1);
        }

        if ($this->object instanceof AdditionalQueryChange) {
            $query = $this->object->additionalQueryChange($query);
        }

        return $query;
    }
}