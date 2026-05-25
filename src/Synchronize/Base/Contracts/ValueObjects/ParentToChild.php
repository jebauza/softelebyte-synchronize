<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Softelebyte\Synchronize\Base\ValueObjects\Service\MultiParentRepoValueObject;

interface ParentToChild
{
    public function parentToChild(MultiParentRepoValueObject $parent): void;
}