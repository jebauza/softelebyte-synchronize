<?php


namespace Softelebyte\Synchronize\Base\Decorator;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\Mysql\InsertUpdateFromCollection;
use Softelebyte\Synchronize\Base\Services\Table\ServiceNotDeleteTable;
use Softelebyte\Synchronize\Base\ValueObjects\Service\RepoNotDelete;

class RepoNotDeleteDecorator implements ServiceDecorator
{
    protected RepoNotDelete $object;

    public function __construct(RepoNotDelete $object)
    {
        $this->object = $object;
    }

    public function instanceService(): ServiceContract
    {
        return new ServiceNotDeleteTable($this->object, $this->insertUpdateRepo());
    }

    public function insertUpdateRepo(): InsertUpdateContract
    {
        return new InsertUpdateFromCollection($this->object);
    }
}