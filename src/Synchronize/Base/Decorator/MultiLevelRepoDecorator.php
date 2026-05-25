<?php


namespace Softelebyte\Synchronize\Base\Decorator;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteInactiveContract;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\Active\DeleteMultiInactive;
use Softelebyte\Synchronize\Base\Repository\Mysql\InsertUpdateFromMultiParent;
use Softelebyte\Synchronize\Base\Services\Table\ServiceTable;
use Softelebyte\Synchronize\Base\ValueObjects\Service\MultiParentRepoValueObject;

class MultiLevelRepoDecorator implements ServiceDecorator
{
    protected MultiParentRepoValueObject $object;

    public function __construct(MultiParentRepoValueObject $object)
    {
        $this->object = $object;
    }

    public function instanceService(): ServiceContract
    {
        return new ServiceTable($this->object, $this->deleteRepo(), $this->insertUpdateRepo());
    }

    public function deleteRepo(): DeleteInactiveContract
    {
        return new DeleteMultiInactive($this->object);
    }

    public function insertUpdateRepo(): InsertUpdateContract
    {
        return new InsertUpdateFromMultiParent($this->object);
    }
}