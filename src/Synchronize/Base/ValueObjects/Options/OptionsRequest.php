<?php

namespace Softelebyte\Synchronize\Base\ValueObjects\Options;

use Softelebyte\Synchronize\Base\Contracts\Container\OptionsRequestContract;

class OptionsRequest implements OptionsRequestContract
{
    protected array $options;

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function get(string $key): ?string
    {
        return $this->options[$key] ?? null;
    }
}