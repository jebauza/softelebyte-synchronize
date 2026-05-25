<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo\FromTable;


use Illuminate\Database\Query\Builder;

interface SqlMaker
{
    public function getPrincipalQuery(): Builder;
}