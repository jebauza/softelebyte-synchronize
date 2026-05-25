<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;

use Illuminate\Database\Query\Builder;


interface AdditionalQueryChange
{
    public function additionalQueryChange(Builder $builder): Builder;
}
