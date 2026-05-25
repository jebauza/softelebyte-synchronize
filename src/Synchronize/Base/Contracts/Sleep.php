<?php

namespace Softelebyte\Synchronize\Base\Contracts;

interface Sleep
{
    /**
     * sleep in seconds
     * @return float
     */
    public function secondsToSleep(): float;
}