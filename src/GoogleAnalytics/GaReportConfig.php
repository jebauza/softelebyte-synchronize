<?php

namespace Softelebyte\GoogleAnalytics;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Metric;

final class GaReportConfig
{
    private string $propertyId;
    private DateRange $dateRange;
    private array $metrics = [];
    private array $dimensions = [];
    private int $limit = 100000;
    private int $offset = 0;
    private ?FilterExpression $dimensionsFilter = null;
    private ?FilterExpression $metricsFilter = null;
    private array $extraConfig = [];

    public static function make(): self
    {
        return new self();
    }

    public function setProperty(string $propertyId): self
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    public function property(): string
    {
        return $this->propertyId;
    }

    public function setDateRange(DateTimeInterface|CarbonInterface $start, DateTimeInterface|CarbonInterface $end): self
    {
        $this->dateRange = (new DateRange)
            ->setStartDate($start->format('Y-m-d'))
            ->setEndDate($end->format('Y-m-d'));

        return $this;
    }

    public function dateRange(): DateRange
    {
        return $this->dateRange;
    }

    public function setMetrics(array $metrics): self
    {
        $this->metrics = array_map(
            fn(string $metric) => new Metric(['name' => $metric]),
            $metrics
        );

        return $this;
    }

    public function metrics(): array
    {
        return $this->metrics;
    }

    public function setDimensions(array $dimensions): self
    {
        $this->dimensions = array_map(
            fn(string $dimension) => new Dimension(['name' => $dimension]),
            $dimensions
        );

        return $this;
    }

    public function dimensions(): array
    {
        return $this->dimensions;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function setDimensionsFilters(FilterExpression $filters): self
    {
        $this->dimensionsFilter = $filters;

        return $this;
    }

    public function dimensionsFilters(): ?FilterExpression
    {
        return $this->dimensionsFilter;
    }

    public function setMetricsFilters(FilterExpression $filters): self
    {
        $this->metricsFilter = $filters;

        return $this;
    }

    public function metricsFilters(): ?FilterExpression
    {
        return $this->metricsFilter;
    }

    public function setExtra(array $extra): self
    {
        $this->extraConfig = $extra;

        return $this;
    }

    public function extra(): array
    {
        return $this->extraConfig;
    }
}
