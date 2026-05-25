<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Illuminate\Database\Query\Builder;

interface NotPrimaryQuery
{
    public function notPrimaryQuery(Builder $builder): Builder;

}