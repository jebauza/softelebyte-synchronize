<?php

namespace Softelebyte\QueryFilters\Builder;

use Illuminate\Database\Eloquent\Builder;
use Softelebyte\QueryFilters\Concerns\ApplyFilters;
use Softelebyte\QueryFilters\Concerns\AvoidDuplicateJoin;

class QueryFiltersBuilder extends Builder
{
    use ApplyFilters;
    use AvoidDuplicateJoin;
}
