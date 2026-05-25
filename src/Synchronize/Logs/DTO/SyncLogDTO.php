<?php


namespace Softelebyte\Synchronize\Logs\DTO;


use DateTime;

class SyncLogDTO
{
    public string $id;
    public string $config;
    public ?string $syncId;
    public ?string $syncStatusId;
    public DateTime $date_start;
    public ?Datetime $date_end;
}
