<?php


namespace Softelebyte\Synchronize\Base\Contracts\Queues;


use Illuminate\Contracts\Queue\ShouldQueue;

interface SetShouldQueue extends UseQueues
{
    public function shouldQueue(): ShouldQueue;
}