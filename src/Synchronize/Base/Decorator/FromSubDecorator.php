<?php


namespace Softelebyte\Synchronize\Base\Decorator;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\DeleteInactiveContract;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertUpdateContract;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\ActiveUpdate\DeleteInactive;
use Softelebyte\Synchronize\Base\Repository\InsertUpdate\FromSubTable;
use Softelebyte\Synchronize\Base\Repository\Mysql\InsertUpdateFromTable;
use Softelebyte\Synchronize\Base\Services\Table\ServiceTable;
use Softelebyte\Synchronize\Base\ValueObjects\Service\FromSubTableValueObject;

class FromSubDecorator implements ServiceDecorator
{
    protected FromSubTableValueObject $object;

    public function __construct(FromSubTableValueObject $object)
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
        $sqlMaker = new FromSubTable($this->object);
        return new InsertUpdateFromTable($this->object, $sqlMaker);
    }
}