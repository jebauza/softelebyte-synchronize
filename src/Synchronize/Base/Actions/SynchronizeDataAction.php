<?php


namespace Softelebyte\Synchronize\Base\Actions;


use ReflectionException;
use Softelebyte\OutputHelper\OutputHelper;
use Softelebyte\Synchronize\Base\Contracts\Actions\OnFinally;
use Softelebyte\Synchronize\Base\Contracts\Actions\SynchronizeAction;
use Softelebyte\Synchronize\Base\Contracts\Container\SynchronizeContainerContract;
use Softelebyte\Synchronize\Base\Contracts\OnFinish;
use Softelebyte\Synchronize\Base\Contracts\OnStart;
use Softelebyte\Synchronize\Base\Contracts\Row\RowClass;
use Softelebyte\Synchronize\Base\Contracts\Sleep;
use Softelebyte\Synchronize\Base\Exceptions\ThisValueObjetDontHaveDefaultService;
use Softelebyte\Synchronize\Logs\Contracts\LogService;

class SynchronizeDataAction implements SynchronizeAction, OnStart, OnFinish
{

    use OutputHelper;

    private array $options;
    private LogService $logService;
    private SynchronizeContainerContract $container;
    private RowClass $rowClass;

    public function __construct(LogService $logService, SynchronizeContainerContract $container)
    {
        $this->logService = $logService;
        $this->container = $container;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param RowClass $rowClass
     * @throws ThisValueObjetDontHaveDefaultService|ReflectionException
     */
    public function run(RowClass $rowClass): void
    {
        if ($this->skipAction()) {
            dump('Skip action ' . get_class($rowClass));
            return;
        }
        $this->rowClass = $rowClass;
        try {
            $this->onStart();
            foreach ($this->rowClass->rowClass() as $class) {
                $this->write(' ' . $class);
                $this->logService->startConfig($class);
                $this->serviceAndHandle($class);
                $this->logService->endConfig();
                $this->advanceBar();
                $this->sleep();
            }
            $this->onFinish();
        } finally {
            if($this instanceof OnFinally) {
                $this->onFinally();
            }
        }
    }

    public function skipAction(): bool
    {
        return false;
    }

    public function onStart(): void
    {
        if (!$this->enableOutput()) {
            return;
        }
        dump('Empezando a importar ' . get_class($this->rowClass));
        $rowClassArray = $this->rowClass->rowClass();
        $this->initializeOutput($rowClassArray);
    }

    public function enableOutput(): bool
    {
        return true;
    }

    /**
     * @param string $classToRun
     * @throws ThisValueObjetDontHaveDefaultService
     * @throws ReflectionException
     */
    private function serviceAndHandle(string $classToRun): void
    {
        $service = $this->container->getService($classToRun);
        $service->handle();
    }

    public function onFinish(): void
    {
        if (!$this->enableOutput()) {
            return;
        }
        $this->finishBar();
        dump(' ');
    }

    public function rowClass(): RowClass
    {
        return $this->rowClass;
    }

    public function setRowClass(RowClass $rowClass)
    {
        $this->rowClass = $rowClass;
    }

    private function sleep(): void
    {
        if ($this instanceof Sleep) {
            $seconds = $this->secondsToSleep();
            dump('Waiting ' . $seconds . ' seconds');
            sleep($seconds);
        }
    }
}
