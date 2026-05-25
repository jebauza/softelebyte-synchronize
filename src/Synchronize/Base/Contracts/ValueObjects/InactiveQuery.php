<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Illuminate\Database\Query\Builder;

interface InactiveQuery
{
    public function inactiveQuery(Builder $builder): Builder;
}
