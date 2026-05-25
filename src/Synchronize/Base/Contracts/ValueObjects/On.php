<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Closure;

interface On
{
    public function on(): Closure;

}
