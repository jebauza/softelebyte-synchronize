<?php


namespace Softelebyte\Synchronize\Base\Contracts\ValueObjects;


use Softelebyte\Synchronize\Base\Contracts\Repo\Data\Repo;

interface RepoContract
{
    public function repo(): Repo;
}
