<?php


namespace Softelebyte\Synchronize\Base\Services;


use Carbon\Carbon;
use Exception;
use Spatie\LaravelIgnition\Recorders\QueryRecorder\QueryRecorder;
use TypeError;
use Softelebyte\Synchronize\Base\Actions\SynchronizeDataAction;
use Softelebyte\Synchronize\Base\Container\SynchronizeContainer;
use Softelebyte\Synchronize\Base\Contracts\Actions\SynchronizeAction;
use Softelebyte\Synchronize\Base\Contracts\Container\OptionsRequestContract;
use Softelebyte\Synchronize\Base\Contracts\Container\SynchronizeContainerContract;
use Softelebyte\Synchronize\Base\Contracts\Row\ActionChange;
use Softelebyte\Synchronize\Base\Contracts\Row\GroupRowClass;
use Softelebyte\Synchronize\Base\Contracts\Row\HasCallBackService;
use Softelebyte\Synchronize\Base\Contracts\Row\RowClass;
use Softelebyte\Synchronize\Base\Contracts\Services\CallBackService;
use Softelebyte\Synchronize\Base\Contracts\Services\SynchronizeDataServiceContract;
use Softelebyte\Synchronize\Base\Exceptions\GroupIncorrectTypeException;
use Softelebyte\Synchronize\Base\Helper\GroupRowClassHelper;
use Softelebyte\Synchronize\Base\Helper\SyncDateHelper;
use Softelebyte\Synchronize\Logs\Contracts\LogService;

class SynchronizeDataService implements SynchronizeDataServiceContract
{
    protected LogService $logService;
    protected SynchronizeContainer $container;
    protected GroupRowClass $groupRowClass;
    protected ?string $syncHour;
    protected Carbon $syncDate;
    private array $options;
    private array $callbacks = [];

    /**
     * SynchronizeDataServiceBase constructor.
     * @param LogService $logService
     * @param SynchronizeContainerContract $container
     */
    public function __construct(LogService $logService, SynchronizeContainerContract $container)
    {
        /*
         * Importante limitar el número maximo de log de Laravel
         * Laravel por defecto acumula en una variable cada una de las queries que se hacen en una variable
         * Esto incrementa la memoria en infinito
         * Con app(QueryRecorder::class)->setMaxQueries(1) limitamos el maximo que puede acumular esta variable
         */
        app(QueryRecorder::class)->setMaxQueries(1);
        //---------------------------------------------------------------------------
        $this->logService = $logService;
        $this->container = $container;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @throws GroupIncorrectTypeException
     */
    public function handle()
    {
        $optionsRequest = resolve(OptionsRequestContract::class);
        $optionsRequest->setOptions($this->options);
        try {
            if ($this->groupRowClass instanceof HasCallBackService) {
                $services = $this->groupRowClass->dataBaseService();
                foreach ($services as $serviceClass) {
                    $service = resolve($serviceClass);
                    if (!($service instanceof CallBackService)) {
                        throw new TypeError('Service must be an instance of ' . CallBackService::class . ', ' .
                            $serviceClass . ' given.');
                    }
                    $callback = $service->handle();
                    $this->callbacks[] = $callback;
                }
            }
            foreach ($this->groupRowClass->groupRowClass() as $class) {
                $rowClass = new $class();
                $action = $this->instanceAction($rowClass);
                $action->run($rowClass);
            }
        } catch (Exception $exception) {
            $this->reportError($exception);
            throw $exception;
        } finally {
            foreach ($this->callbacks as $callback) {
                $callback();
            }
        }
    }

    protected function instanceAction(RowClass $rowClass): SynchronizeAction
    {
        if ($rowClass instanceof ActionChange) {
            return $rowClass->action($this->logService, $this->container);
        }
        return $this->defaultAction();
    }

    protected function defaultAction(): SynchronizeAction
    {
        return new SynchronizeDataAction($this->logService, $this->container);
    }

    public function reportError($exception): void
    {
        $this->logService->reportError($exception);
    }

    /**
     * @throws GroupIncorrectTypeException
     */
    public function onStart(): void
    {
        $this->processOptions();
        $this->logService->start($this->syncDate);
    }

    /**
     * @throws GroupIncorrectTypeException
     */
    protected function processOptions(): void
    {
        $this->setGroupRowClass();
        $this->syncHour = $this->options['synchour'] ?? null;
        $syncDateHelper = new SyncDateHelper($this->groupRowClass, $this->syncHour);
        $this->syncDate = $syncDateHelper->syncDate($this->syncHour);
    }

    public function onFinish(): void
    {
        $this->logService->end();
    }

    /**
     * @return GroupRowClass
     */
    public function getGroupRowClass(): GroupRowClass
    {
        return $this->groupRowClass;
    }

    /**
     * @throws GroupIncorrectTypeException
     */
    public function setGroupRowClass($groupRowClass = null): void
    {
        if ($groupRowClass) {
            $this->groupRowClass = $groupRowClass;
            return;
        }
        $groupRowClassHelper = new GroupRowClassHelper($this->options);
        $this->groupRowClass = $groupRowClassHelper->groupRowClass();
    }
}
