<?php


namespace Softelebyte\Synchronize\Base\Command;


use Exception;
use Illuminate\Console\Command;
use Softelebyte\Synchronize\Base\Contracts\Container\OptionsRequestContract;
use Softelebyte\Synchronize\Base\Contracts\Queues\UseQueues;
use Softelebyte\Synchronize\Base\Contracts\Services\SynchronizeDataServiceContract;
use Softelebyte\Synchronize\Base\Helper\QueueServiceHelper;
use Softelebyte\Synchronize\Base\Helper\SynchronizeDataExecuteHelper;
use Softelebyte\Synchronize\Base\ValueObjects\Options\OptionsRequest;


class SynchronizeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synchronize:data {--synchour=} {--group=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza datos';

    private SynchronizeDataServiceContract $synchronizeDataService;

    public function __construct(SynchronizeDataServiceContract $configService)
    {
        parent::__construct();
        $this->synchronizeDataService = $configService;
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->synchronizeDataService->setOptions($this->options());
        $this->synchronizeDataService->setGroupRowClass();
        if ($this->synchronizeDataService->getGroupRowClass() instanceof UseQueues) {
            $queueHelper = new QueueServiceHelper($this->synchronizeDataService);
            $queueHelper->handle();
            return;
        }
        $synchronizeDataExecuteHelper = new SynchronizeDataExecuteHelper($this->synchronizeDataService);
        $synchronizeDataExecuteHelper->handle();
    }
}
