<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo;


interface DeleteInactiveContract extends DeleteContract
{
    public function inactive(): void;

    public function rollback(): void;
}