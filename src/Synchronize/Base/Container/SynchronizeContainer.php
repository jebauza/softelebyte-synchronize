<?php


namespace Softelebyte\Synchronize\Base\Container;


use ReflectionClass;
use ReflectionException;
use TypeError;
use Softelebyte\Synchronize\Base\Contracts\Container\SynchronizeContainerContract;
use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\SynchronizeValueObject;
use Softelebyte\Synchronize\Base\Exceptions\ThisValueObjetDontHaveDefaultService;

class SynchronizeContainer implements SynchronizeContainerContract
{
    protected array $container = [];

    public function __construct()
    {
        $this->container = config('synchronize.services') ?? [];
    }

    public function addNewServiceToContainer(string $valueObject, string $storeService)
    {
        if (!is_subclass_of($valueObject, SynchronizeValueObject::class)) {
            throw new TypeError('Argument 1 passed to Softelebyte\Synchronize\Base\Container\SynchronizeContainer::addNewServiceToContainer() ' .
                'must be an instance of ' . SynchronizeValueObject::class . ', ' . $valueObject . ' given.');
        }
        if (!is_subclass_of($storeService, ServiceDecorator::class)) {
            throw new TypeError('Argument 2 passed to Softelebyte\Synchronize\Base\Container\SynchronizeContainer::addNewServiceToContainer() ' .
                'must be an instance of ' . ServiceDecorator::class . ', ' . $storeService . ' given.');
        }
        $this->container[] = [$valueObject, $storeService];
    }

    /**
     * @param string $thisValueObject
     * @return ServiceContract
     * @throws ReflectionException
     * @throws ThisValueObjetDontHaveDefaultService
     */
    public function getService(string $thisValueObject): ServiceContract
    {
        $decorator = $this->getDecorator($thisValueObject);
        $instanceValueObject = new $thisValueObject();
        if (!is_subclass_of($instanceValueObject, SynchronizeValueObject::class)) {
            throw new TypeError('ExportConfig with index: ' . $instanceValueObject . ' must be an instance of ' .
                SynchronizeValueObject::class . ', ' . $instanceValueObject . ' given.');
        }
        $instanceDecorator = new $decorator($instanceValueObject);
        return $instanceDecorator->instanceService();
    }

    /**
     * @param string $thisValueObject
     * @return mixed|ServiceDecorator
     * @throws ReflectionException
     * @throws ThisValueObjetDontHaveDefaultService
     */
    public function getDecorator(string $thisValueObject)
    {
        $reflectionClass = new ReflectionClass($thisValueObject);
        do {
            $parentClass = $reflectionClass->getParentClass();
            if (!$parentClass) {
                throw new ThisValueObjetDontHaveDefaultService($thisValueObject);
            }
            $reflectionClass = $parentClass;
            $parentName = $parentClass->getName();
            $found = isset($this->container[$parentName]);
        } while (!$found);

        $decorator = $this->container[$parentName];
        if (!is_subclass_of($decorator, ServiceDecorator::class)) {
            throw new TypeError('ExportConfig with index: ' . $thisValueObject . ' must be an instance of ' .
                ServiceDecorator::class . ', ' . $decorator . ' given.');
        }
        return $decorator;
    }
}
