<?php

namespace Softelebyte\QueryFilters\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Softelebyte\QueryFilters\Filters\QueryFilter;

trait ApplyFilters
{
    public function addFilter(QueryFilter $filter): static
    {
        $scope = $this->createScope($filter);
        $this->withGlobalScope($filter->id(), $scope);

        return $this;
    }

    public function addFilters(array|QueryFilter $filters): static
    {
        $filters = $filters instanceof QueryFilter ? [$filters] : $filters;

        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    public function removeFilter(string $filterClass, ?string $field = null): static
    {
        /** @var QueryFilter $filter */
        $filter = new $filterClass('', $field);

        $this->withoutGlobalScope($filter->id());

        return $this;
    }

    public function changeFilter(string $filterClass, string $fieldName, ?string $fromField = null): static
    {
        $filterId = (new $filterClass('', $fromField))->id();

        /** @var QueryFilter $filter */
        $filter = $this->scopes[$filterId]?->filter();

        if ($filter) {
            $filter->setField($fieldName);
            $this->withoutGlobalScope($filterId);
            $this->addFilter($filter);
        }

        return $this;
    }

    public function clearFilters(): static
    {
        $this->withoutGlobalScopes();

        return $this;
    }

    private function createScope(QueryFilter $filter): Scope
    {
        return new class($filter) implements Scope {
            public function __construct(private readonly QueryFilter $filter)
            {
            }

            public function apply(Builder $builder, Model $model): void
            {
                $this->filter->applyFilter($builder);
            }

            public function filter(): QueryFilter
            {
                return $this->filter;
            }
        };
    }
}
