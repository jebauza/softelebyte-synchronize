<?php


namespace Softelebyte\Synchronize\Base\Decorator\SqlServer;


use Softelebyte\Synchronize\Base\Contracts\Decorator\ServiceDecorator;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertContact;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Repository\TruncateAndInsert\FromSubInsert;
use Softelebyte\Synchronize\Base\Services\Table\TruncateAndInsert;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupTruncateSubValueObject;

class GroupSubTruncateDecorator implements ServiceDecorator
{
    protected GroupTruncateSubValueObject $object;

    public function __construct(GroupTruncateSubValueObject $object)
    {
        $this->object = $object;
    }

    public function instanceService(): ServiceContract
    {
        return new TruncateAndInsert($this->object, $this->insertRepo());
    }

    public function insertRepo(): InsertContact
    {
        return new FromSubInsert($this->object);
    }
}