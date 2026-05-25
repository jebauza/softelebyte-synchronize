<?php


namespace Softelebyte\Synchronize\Base\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;
use Softelebyte\Synchronize\Base\Models\BaseModel;

class Active implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model | BaseEtlModel | BaseModel $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $builder->getQuery()->from;

        if(str_contains($table, ' as ')) {
            $table = explode(' as ', $table)[1];
        }

        $builder->where($table . '.' . $model::activeDefault(), '=', 1);
    }
}
