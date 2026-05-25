<?php


namespace Softelebyte\Synchronize\Base\Helper;


use Softelebyte\Synchronize\Base\Contracts\Services\SynchronizeDataServiceContract;

class SynchronizeDataExecuteHelper
{
    private SynchronizeDataServiceContract $synchronizeDataService;

    public function __construct(SynchronizeDataServiceContract $synchronizeDataService)
    {
        $this->synchronizeDataService = $synchronizeDataService;
    }

    public function handle()
    {
        $this->synchronizeDataService->onStart();
        $this->synchronizeDataService->handle();
        $this->synchronizeDataService->onFinish();
    }
}