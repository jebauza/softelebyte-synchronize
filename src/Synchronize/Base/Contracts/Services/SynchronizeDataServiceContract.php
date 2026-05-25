<?php

namespace Softelebyte\Synchronize\Base\Contracts\Services;

use Softelebyte\Synchronize\Base\Contracts\OnFinish;
use Softelebyte\Synchronize\Base\Contracts\OnStart;
use Softelebyte\Synchronize\Base\Contracts\Row\GroupRowClass;

interface SynchronizeDataServiceContract extends OnStart, OnFinish
{
    public function setOptions(array $options);

    public function handle();

    public function getGroupRowClass(): GroupRowClass;

    public function setGroupRowClass(): void;

    public function reportError($exception): void;
}