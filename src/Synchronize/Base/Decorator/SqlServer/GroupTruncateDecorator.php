<?php


namespace Softelebyte\Synchronize\Base\Decorator\SqlServer;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertContact;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\TruncateAndInsert\GroupInsert;
use Softelebyte\Synchronize\Base\Services\Table\TruncateAndInsert;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupTruncateValueObject;

class GroupTruncateDecorator implements ServiceDecorator
{
    protected GroupTruncateValueObject $object;

    public function __construct(GroupTruncateValueObject $object)
    {
        $this->object = $object;
    }

    public function instanceService(): ServiceContract
    {
        return new TruncateAndInsert($this->object, $this->insertRepo());
    }

    public function insertRepo(): InsertContact
    {
        return new GroupInsert($this->object);
    }
}