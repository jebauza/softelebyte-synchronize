<?php


namespace Softelebyte\Synchronize\Base\Contracts\Row;


use Softelebyte\Synchronize\Base\Contracts\Actions\SynchronizeAction;

interface GroupRowClassOtherAction
{
    public function action(): SynchronizeAction;
}
