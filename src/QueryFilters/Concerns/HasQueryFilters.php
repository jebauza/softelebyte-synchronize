<?php

namespace Softelebyte\QueryFilters\Concerns;

use Softelebyte\QueryFilters\Builder\QueryFiltersBuilder;

trait HasQueryFilters
{
    public function newEloquentBuilder($query): QueryFiltersBuilder
    {
        return new QueryFiltersBuilder($query);
    }

    public static function query(): QueryFiltersBuilder
    {
        return parent::query();
    }
}
