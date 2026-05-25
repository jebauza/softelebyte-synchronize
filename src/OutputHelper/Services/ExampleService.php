<?php

namespace Softelebyte\OutputHelper\Services;

use Softelebyte\OutputHelper\OutputHelper;

class ExampleService
{
    use OutputHelper;

    const ROW_CLASS = [
        'Config 1',
        'Config 2',
        'Config 3',
    ];

    public function handle()
    {
        $rowClass = self::ROW_CLASS;
        $this->initializeOutput($rowClass);
        foreach ($rowClass as $class) {
            $this->write(' ' . $class);
            $this->advanceBar();
        }
        $this->finishBar();
        dump(' ');
    }
}