<?php


namespace Softelebyte\Synchronize\Base\Helper;


use Carbon\Carbon;
use Softelebyte\Synchronize\Base\Contracts\Helper\SyncDateTransform;
use Softelebyte\Synchronize\Base\Contracts\Row\GroupRowClass;

class SyncDateHelper
{
    private GroupRowClass $groupRowClass;
    private ?string $syncHour;

    /**
     * SyncDateHelper constructor.
     * @param GroupRowClass $groupRowClass
     * @param string|null $syncHour
     */
    public function __construct(GroupRowClass $groupRowClass, ?string $syncHour)
    {
        $this->groupRowClass = $groupRowClass;
        $this->syncHour = $syncHour;
    }

    public function syncDate(?string $syncHour): Carbon
    {
        $syncDate = Carbon::now()->second(0);
        if ($syncHour) {
            $segments = explode(':', $syncHour);
            $syncDate->hours($segments[0])->minutes($segments[1]);
        }
        $this->syncDateTransform($syncDate);
        return $syncDate;
    }

    private function syncDateTransform(Carbon $syncDate): void
    {
        if ($this->groupRowClass instanceof SyncDateTransform) {
            $this->groupRowClass->syncDateTransform($syncDate);
        }
    }
}
