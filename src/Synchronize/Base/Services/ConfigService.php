<?php

namespace Softelebyte\Synchronize\Base\Services;

use Softelebyte\Synchronize\Base\Contracts\Row\HasInfo;
use Softelebyte\Synchronize\Base\Contracts\Row\HideInfo;
use Softelebyte\Synchronize\Base\ValueObjects\ExportConfig\SynchroConfigValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\ExportConfig\SynchroTypeValueObject;

class ConfigService
{
    public function groupTypes() : SynchroConfigValueObject
    {
        $synchroConfigValueObject = new SynchroConfigValueObject();
        $config = config('synchronize.alias');

        foreach ($config as $key => $class) {
            if(is_subclass_of($class,HideInfo::class)) {
                continue;
            }
            $synchroTypeValueObject = new SynchroTypeValueObject();
            $synchroTypeValueObject->group = $key;
            if(is_subclass_of($class,HasInfo::class)) {
                $synchroTypeValueObject->name = $class::name();
                $synchroTypeValueObject->description = $class::description();
            }
            $synchroConfigValueObject->types[] = $synchroTypeValueObject;
        }

        $defaultDatabase = config('database.default');

        $synchroConfigValueObject->database = config('database.connections.' . $defaultDatabase . '.database');

        return $synchroConfigValueObject;
    }
}