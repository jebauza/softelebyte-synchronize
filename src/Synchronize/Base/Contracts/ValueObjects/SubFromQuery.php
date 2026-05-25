<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Closure;
use Illuminate\Database\Query\Builder;

interface SubFromQuery
{
    /**
     * @return Closure|Builder|string
     */
    public function subFromQuery();

}
