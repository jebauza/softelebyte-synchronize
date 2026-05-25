<?php

namespace Softelebyte\Synchronize\Base\ValueObjects\ExportConfig;

use Illuminate\Contracts\Support\Arrayable;
use Softelebyte\Synchronize\Base\Transformer\ToArrayAllProperties;

class SynchroConfigValueObject implements Arrayable
{
    use ToArrayAllProperties;

    public array $types = [];
    public string $database = '';
}