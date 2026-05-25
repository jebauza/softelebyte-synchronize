<?php

namespace Softelebyte\Synchronize\Base\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Softelebyte\Synchronize\Base\Contracts\Services\SynchronizeDataServiceContract;
use Softelebyte\Synchronize\Base\Helper\SynchronizeDataExecuteHelper;

class SynchronizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected SynchronizeDataServiceContract $synchronizeDataService;

    public function __construct(SynchronizeDataServiceContract $synchronizeDataService)
    {
        $this->synchronizeDataService = $synchronizeDataService;
    }

    public function handle()
    {
        $synchronizeDataExecuteHelper = new SynchronizeDataExecuteHelper($this->synchronizeDataService);
        $synchronizeDataExecuteHelper->handle();
    }

    /**
     * @throws Throwable
     */
    public function failed(Throwable $throwable)
    {
        dump($throwable);
        //$this->synchronizeDataService->reportError($throwable);
        throw $throwable;
    }
}
