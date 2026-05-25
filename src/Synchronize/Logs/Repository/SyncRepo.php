<?php


namespace Softelebyte\Synchronize\Logs\Repository;

use Carbon\Carbon;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Softelebyte\Synchronize\Base\Services\CacheService;
use Softelebyte\Synchronize\Logs\DTO\SyncDTO;
use Softelebyte\Synchronize\Logs\Entities\Binary\Sync as BinarySync;
use Softelebyte\Synchronize\Logs\Entities\Sync;
use Softelebyte\Synchronize\Logs\Entities\SyncStatus;
use Softelebyte\Synchronize\Logs\Fields\SyncStatusFields;


class SyncRepo
{
    /**
     * @var Sync Modelo
     */
    protected string $model = Sync::class;

    public function setModel($model): void
    {
        $this->model = $model;
    }

    public function __construct()
    {
        if((DB::connection() instanceof MySqlConnection)) {
            $this->model = BinarySync::class;
        }
    }

    public function CreateSyncData(Carbon $date): SyncDTO
    {
        $syncModel = new $this->model();
        $syncModel->id = Uuid::uuid4()->toString();
        $syncModel->{$this->model::SYNC_DATA} = $date;
        Cache::pull('SyncStatus');
        $syncStatus = CacheService::getSyncStatus();
        $syncModel->{$this->model::SYNC_STATUS_ID} = $syncStatus->where('name', '=', 'Ejecutando')->first()->id;
        $syncModel->save();

        $syncDTO = new SyncDTO();
        $syncDTO->id = $syncModel->{$this->model::ID};
        $syncDTO->syncData = $syncModel->{$this->model::SYNC_DATA};
        $syncDTO->syncStatusId = $syncModel->{$this->model::SYNC_STATUS_ID};

        return $syncDTO;
    }

    public function save(SyncDTO $syncDTO): void
    {
        $syncModel = $this->model::query()->whereUuid('id', '=', $syncDTO->id)->get()->first();
        $syncModel->{$this->model::SYNC_DATA} = $syncDTO->syncData ?? $syncModel->{$this->model::SYNC_DATA};
        $syncModel->{$this->model::SYNC_STATUS_ID} = $syncDTO->syncStatusId ?? $syncModel->{$this->model::SYNC_STATUS_ID};
        $syncModel->save();
    }

    public function getFirst(): Sync
    {
        $syncStatus = CacheService::getSyncStatus();

        $syncLogSql = $this->model::query();

        $syncLogSql->select($this->model::SYNC_DATA)
            ->whereUuid(
                $this->model::SYNC_STATUS_ID,
                '=',
                $syncStatus->where('name', '=', SyncStatusFields::FINISH)->first()->{SyncStatus::ID}
            )
            ->orderByDesc($this->model::SYNC_DATA);

        return $syncLogSql->first();
    }

    public function getLatestWithError($limit = 10): Collection
    {
        return $this->model::query()->with('syncStatus')->orderByDesc($this->model::SYNC_DATA)->limit($limit)->get();
    }
}
