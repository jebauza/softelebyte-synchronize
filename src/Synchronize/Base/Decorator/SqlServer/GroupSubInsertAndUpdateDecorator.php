<?php


namespace Softelebyte\Synchronize\Base\Decorator\SqlServer;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertAndUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\Delete\DeleteSub;
use Softelebyte\Synchronize\Base\Repository\InsertAndUpdate\FromSubInsertAndUpdate;
use Softelebyte\Synchronize\Base\Services\Table\ServiceNotActiveTable;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupSubValueObject;

class GroupSubInsertAndUpdateDecorator implements ServiceDecorator
{
    protected GroupSubValueObject $object;

    public function __construct(GroupSubValueObject $object)
    {
        $this->object = $object;
    }

    public function instanceService(): ServiceContract
    {
        return new ServiceNotActiveTable($this->object, $this->deleteRepo(), $this->insertRepo());
    }

    public function deleteRepo(): DeleteSub
    {
        return new DeleteSub($this->object);
    }

    public function insertRepo(): InsertAndUpdateContract
    {
        return new FromSubInsertAndUpdate($this->object);
    }
}