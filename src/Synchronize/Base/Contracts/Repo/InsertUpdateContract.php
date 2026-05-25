<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo;


interface InsertUpdateContract
{
    public function insertUpdate(): void;

    public function getOriginCount(): int;
}