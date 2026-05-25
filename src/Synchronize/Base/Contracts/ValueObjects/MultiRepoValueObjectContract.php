<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Softelebyte\Synchronize\Base\Contracts\Repo\Data\MultiRepo;

interface MultiRepoValueObjectContract
{
    public function repo(): MultiRepo;
}
