<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Illuminate\Database\Query\Builder;

interface PrimaryQuery
{
    public function primaryQuery(Builder $builder): Builder;

}