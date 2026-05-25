<?php


namespace Softelebyte\Synchronize\Base\Decorator\SqlServer;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertAndUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\Delete\Delete;
use Softelebyte\Synchronize\Base\Repository\InsertAndUpdate\GroupInsert;
use Softelebyte\Synchronize\Base\Repository\OriginKeys\SelectOriginKeysAlias;
use Softelebyte\Synchronize\Base\Services\Table\ServiceNotActiveTable;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupValueObject;

class GroupInsertAndUpdateDecorator implements ServiceDecorator
{
    protected GroupValueObject $object;

    public function __construct(GroupValueObject $object)
    {
        $this->object = $object;
    }

    public function instanceService(): ServiceContract
    {
        return new ServiceNotActiveTable($this->object, $this->deleteRepo(), $this->insertRepo());
    }


    public function deleteRepo(): Delete
    {
        return new Delete($this->object, new SelectOriginKeysAlias());
    }

    public function insertRepo(): InsertAndUpdateContract
    {
        return new GroupInsert($this->object);
    }
}