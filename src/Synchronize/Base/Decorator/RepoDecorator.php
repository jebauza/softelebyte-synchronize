<?php


namespace Softelebyte\Synchronize\Base\Decorator;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteInactiveContract;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\Active\DeleteInactive;
use Softelebyte\Synchronize\Base\Repository\Mysql\InsertUpdateFromCollection;
use Softelebyte\Synchronize\Base\Services\Table\ServiceTable;
use Softelebyte\Synchronize\Base\ValueObjects\Service\RepoValueObject;

class RepoDecorator implements ServiceDecorator
{
    protected RepoValueObject $object;

    public function __construct(RepoValueObject $object)
    {
        $this->object = $object;
    }

    public function instanceService(): ServiceContract
    {
        return new ServiceTable($this->object, $this->deleteRepo(), $this->insertUpdateRepo());
    }

    public function deleteRepo(): DeleteInactiveContract
    {
        return new DeleteInactive($this->object);
    }

    public function insertUpdateRepo(): InsertUpdateContract
    {
        return new InsertUpdateFromCollection($this->object);
    }
}