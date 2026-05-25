<?php

namespace Softelebyte\QueryFilters\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    public function __construct(
        private readonly mixed $value,
        private ?string $customField = null
    ) {
    }

    abstract public function defaultField(): ?string;

    abstract public function apply(Builder $query, mixed $value = null): void;

    public static function make(mixed $value, ?string $field = null): static
    {
        return new static($value, $field);
    }

    public function applyFilter(Builder $query): void
    {
        if ($this->value === null) {
            return;
        }

        $this->apply($query, $this->value);
    }

    public function field(): ?string
    {
        return $this->customField ?? $this->defaultField();
    }

    public function setField(string $field): static
    {
        $this->customField = $field;

        return $this;
    }

    public function id(): string
    {
        $id = Str::snake(str_replace('Filter', '', last(explode('\\', $this::class))));

        if ($this->customField !== null && $this->customField !== $this->defaultField() && $this->customField !== $id) {
            $id .= '_'.$this->customField;
        }

        return Str::snake($id);
    }
}
