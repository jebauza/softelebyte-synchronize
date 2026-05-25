<?php


namespace Softelebyte\Synchronize\Base\Contracts\Repo\Data;


use IteratorAggregate;

interface Repo
{
    public function getAll(): IteratorAggregate;
}
