<?php


namespace Softelebyte\Synchronize\Base\Contracts\Row;

interface HasInfo
{
    static public function name() : string;
    static public function description() : string;
}
