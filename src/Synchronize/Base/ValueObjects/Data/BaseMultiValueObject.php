<?php


namespace Softelebyte\Synchronize\Base\ValueObjects\Data;


class BaseMultiValueObject
{
    protected array $principal;

    public function __construct(array $principal)
    {
        $this->principal = $principal;
    }

    public function getPrincipal(): array
    {
        return $this->principal;
    }
}
