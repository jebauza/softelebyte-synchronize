<?php


namespace Softelebyte\Synchronize\Logs\Resources;


use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Softelebyte\Synchronize\Logs\Contracts\GetConfigNameToFront;
use Softelebyte\Synchronize\Logs\Fields\LastSynchronizeDateFields;

class LastSynchronizeDateResource extends JsonResource
{
    public function toArray($request)
    {
        $class = $this->config;
        $name = $class;
        if (is_a($class, GetConfigNameToFront::class, true)) {
            $name = $class::getFrontName();
        }
        $date = Carbon::parse($this->{LastSynchronizeDateFields::DATE}, 'Europe/Madrid');
        $offset = $date->utcOffset();
        $date = $date->addMinutes($offset);
        return [
            LastSynchronizeDateFields::NAME => $name,
            LastSynchronizeDateFields::DATE => $date->format('Y-m-d H:i:s'),
        ];
    }
}
