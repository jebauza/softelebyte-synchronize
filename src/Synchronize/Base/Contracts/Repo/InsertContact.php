<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo;


interface InsertContact
{
    public function insert(): void;

    public function getOriginCount(): int;
}