<?php


namespace Softelebyte\Synchronize\Base\Contracts\Services;


interface CallBackService
{
    public function handle(): \Closure;
}