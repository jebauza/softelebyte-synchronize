<?php


namespace Softelebyte\Synchronize\Base\Contracts\Container;


interface OptionsRequestContract
{
    public function setOptions(array $options): void;

    public function get(string $key): ?string;
}