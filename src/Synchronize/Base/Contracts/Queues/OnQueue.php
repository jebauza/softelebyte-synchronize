<?php


namespace Softelebyte\Synchronize\Base\Contracts\Queues;

interface OnQueue extends UseQueues
{
    public function onQueue(): string;
}