<?php


namespace Softelebyte\Synchronize\Logs\Service;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Softelebyte\Synchronize\Base\Services\CacheService;
use Softelebyte\Synchronize\Logs\Actions\SendMail\ErrorSynchronize;
use Softelebyte\Synchronize\Logs\Contracts\LogService;
use Softelebyte\Synchronize\Logs\DTO\SyncDTO;
use Softelebyte\Synchronize\Logs\DTO\SyncErrorDTO;
use Softelebyte\Synchronize\Logs\DTO\SyncLastConfigDto;
use Softelebyte\Synchronize\Logs\DTO\SyncLogDTO;
use Softelebyte\Synchronize\Logs\Fields\SyncStatusFields;
use Softelebyte\Synchronize\Logs\Repository\SyncErrorRepo;
use Softelebyte\Synchronize\Logs\Repository\SyncLastConfigRepo;
use Softelebyte\Synchronize\Logs\Repository\SyncLogRepo;
use Softelebyte\Synchronize\Logs\Repository\SyncRepo;

class SyncLogService implements LogService
{
    public SyncDTO $syncDto;
    public SyncLogDTO $syncLogDto;
    public SyncRepo $syncRepo;
    public SyncLogRepo $syncLogRepo;
    public SyncErrorRepo $syncErrorRepo;
    public SyncLastConfigRepo $syncLastConfigRepo;

    public function __construct(SyncRepo $syncRepo, SyncLogRepo $syncLogRepo, SyncErrorRepo $syncErrorRepo,SyncLastConfigRepo $syncLastConfigRepo)
    {
        $this->syncRepo = $syncRepo;
        $this->syncLogRepo = $syncLogRepo;
        $this->syncErrorRepo = $syncErrorRepo;
        $this->syncLastConfigRepo = $syncLastConfigRepo;
    }

    public function start(Carbon $syncDate): void
    {
        $this->syncDto = $this->syncRepo->CreateSyncData($syncDate);
    }

    public function startConfig(string $configName): void
    {
        $syncStatus = CacheService::getSyncStatus();
        $this->syncLogDto = new SyncLogDTO();
        $this->syncLogDto->config = $configName;
        $this->syncLogDto->date_start = Carbon::now();
        $this->syncLogDto->syncId = isset($this->syncDto) ? $this->syncDto->id : null;
        $this->syncLogDto->syncStatusId = $syncStatus->where('name', '=', SyncStatusFields::EXECUTING)->first()->id;
        $this->syncLogDto = $this->syncLogRepo->insert($this->syncLogDto);
    }

    public function endConfig(): void
    {
        $syncStatus = CacheService::getSyncStatus();
        $this->syncLogDto->date_end = Carbon::now();
        $this->syncLogDto->syncStatusId = $syncStatus->where('name', '=', SyncStatusFields::FINISH)->first()->id;
        $this->syncLogRepo->update($this->syncLogDto);

        $syncLastConfigDto = new SyncLastConfigDto();
        $syncLastConfigDto->config = $this->syncLogDto->config;
        $syncLastConfigDto->syncLogId = $this->syncLogDto->id;
        $this->syncLastConfigRepo->insertOrUpdate($syncLastConfigDto);
    }

    public function end(): void
    {
        $syncStatus = CacheService::getSyncStatus();
        $this->syncDto->syncStatusId = $syncStatus->where('name', '=', SyncStatusFields::FINISH)->first()->id;
        $this->syncRepo->save($this->syncDto);
    }

    public function reportError(Exception $exception): void
    {
        if(!isset($this->syncDto)) {
            $this->start(Carbon::now());
        }
        
        $syncStatus = CacheService::getSyncStatus();
        $statusError = $this->syncDto->syncStatusId = $syncStatus->where('name', '=', SyncStatusFields::ERROR)->first()->id;

        if(isset($this->syncLogDto)) {
            $this->syncLogDto->date_end = Carbon::now();
            $this->syncLogDto->syncStatusId = $statusError;
            $this->syncLogRepo->update($this->syncLogDto);
        }

        $this->syncDto->syncStatusId = $statusError;
        $this->syncRepo->save($this->syncDto);

        $syncErrorDto = new SyncErrorDTO();
        $syncErrorDto->SyncId = $this->syncDto->id;
        $syncErrorDto->error = $exception->getMessage();
        $this->syncErrorRepo->insert($syncErrorDto);

        $errorEmail = config('synchronize.error_email_receptor');
        if (isset($errorEmail)) {
            $errorEmail = explode(';',$errorEmail);
            Mail::to($errorEmail)->send(new ErrorSynchronize($exception->getMessage()));
        }
    }
}
