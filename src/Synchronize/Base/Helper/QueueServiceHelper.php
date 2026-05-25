<?php


namespace Softelebyte\Synchronize\Base\Helper;


use Illuminate\Foundation\Bus\Dispatchable;
use Softelebyte\Synchronize\Base\Contracts\Queues\JobQueue;
use Softelebyte\Synchronize\Base\Contracts\Queues\OnQueue;
use Softelebyte\Synchronize\Base\Contracts\Queues\SetShouldQueue;
use Softelebyte\Synchronize\Base\Contracts\Row\GroupRowClass;
use Softelebyte\Synchronize\Base\Contracts\Services\SynchronizeDataServiceContract;
use Softelebyte\Synchronize\Base\Jobs\SynchronizeJob;

class QueueServiceHelper
{
    const QUEUE = 'default';
    private SynchronizeDataServiceContract $synchronizeDataService;
    private GroupRowClass $groupRowClass;


    public function __construct(SynchronizeDataServiceContract $synchronizeDataService)
    {
        $this->synchronizeDataService = $synchronizeDataService;
        $this->groupRowClass = $synchronizeDataService->getGroupRowClass();
    }

    public function handle(): void
    {
        if ($this->groupRowClass instanceof SetShouldQueue) {
            $this->groupRowClass->shouldQueue();
            return;
        }
        $queue = self::QUEUE;
        if ($this->groupRowClass instanceof OnQueue) {
            $queue = $this->groupRowClass->onQueue();
        }
        /** @var Dispatchable $job */
        $job = SynchronizeJob::class;
        if ($this->groupRowClass instanceof JobQueue) {
            $job = $this->groupRowClass->job();
        }
        $job::dispatch($this->synchronizeDataService)->onQueue($queue);

    }
}
