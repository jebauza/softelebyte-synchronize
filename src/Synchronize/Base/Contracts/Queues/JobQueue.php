<?php


namespace Softelebyte\Synchronize\Base\Contracts\Queues;


interface JobQueue extends UseQueues
{
    public function job(): string;
}