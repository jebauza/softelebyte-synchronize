<?php

namespace Softelebyte\Synchronize\Base\ValueObjects\ExportConfig;

use Illuminate\Contracts\Support\Arrayable;
use Softelebyte\Synchronize\Base\Transformer\ToArrayAllProperties;

class SynchroTypeValueObject implements Arrayable
{
    use ToArrayAllProperties;

    public string $group = '';
    public string $name = '';
    public string $description = '';
}