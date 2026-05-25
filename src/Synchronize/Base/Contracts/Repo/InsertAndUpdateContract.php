<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo;


interface InsertAndUpdateContract extends InsertContact
{
    public function update(): void;
}