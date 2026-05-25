<?php


namespace Softelebyte\Synchronize\Logs\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Softelebyte\Builder\SoftelebyteBuilder;
use Softelebyte\Synchronize\Logs\DTO\SyncLogDTO;
use Softelebyte\Synchronize\Logs\Entities\Binary\SyncLog as BinarySyncLog;
use Softelebyte\Synchronize\Logs\Entities\SyncLog;
use Softelebyte\Synchronize\Logs\Filter\SyncLogFilter;

class SyncLogRepo
{
    /**
     * @var SyncLog modelo
     */
    protected string $model = SyncLog::class;

    protected array $bindings;

    public function __construct()
    {
        if((DB::connection() instanceof MySqlConnection)) {
            $this->model = BinarySyncLog::class;
        }
    }

    function insert(SyncLogDTO $SyncLogDto): SyncLogDTO
    {
        $syncLogModel = new $this->model();
        $syncLogModel->id = Uuid::uuid4()->toString();
        $syncLogModel->{$this->model::SYNC_ID} = isset($SyncLogDto->syncId) ? $SyncLogDto->syncId : null;
        $syncLogModel->{$this->model::SYNC_STATUS_ID} = $SyncLogDto->syncStatusId;
        $syncLogModel->{$this->model::CONFIG} = $SyncLogDto->config;
        $syncLogModel->{$this->model::DATE_START} = $SyncLogDto->date_start;
        $syncLogModel->save();

        $syncDTO = new SyncLogDTO();
        $syncDTO->id = $syncLogModel->{$this->model::ID};
        $syncDTO->syncId = isset($syncLogModel->{$this->model::SYNC_ID}) ? $syncLogModel->{$this->model::SYNC_ID} : null;
        $syncDTO->syncStatusId = bin2hex($syncLogModel->{$this->model::SYNC_STATUS_ID});
        $syncDTO->config = $syncLogModel->{$this->model::CONFIG};
        $syncDTO->date_start = $syncLogModel->{$this->model::DATE_START};
        $syncDTO->date_end = $syncLogModel->{$this->model::DATE_END};

        return $syncDTO;
    }

    function update(SyncLogDTO $SyncLogDto): void
    {
        $syncLogModel = $this->model::query()->whereUuid('id', '=', $SyncLogDto->id)->get()->first();
        $syncLogModel->{$this->model::SYNC_ID} = isset($SyncLogDto->syncId) ? $SyncLogDto->syncId : $syncLogModel->{$this->model::SYNC_ID};
        $syncLogModel->{$this->model::SYNC_STATUS_ID} = isset($SyncLogDto->syncStatusId) ? $SyncLogDto->syncStatusId : $syncLogModel->{$this->model::SYNC_STATUS_ID};
        $syncLogModel->{$this->model::CONFIG} = $SyncLogDto->config ?? $syncLogModel->{$this->model::CONFIG};
        $syncLogModel->{$this->model::DATE_START} = $SyncLogDto->date_start ?? $syncLogModel->{$this->model::DATE_START};
        $syncLogModel->{$this->model::DATE_END} = $SyncLogDto->date_end ?? $syncLogModel->{$this->model::DATE_END};
        $syncLogModel->save();
    }

    function getFirst(SyncLogFilter $Filter): SoftelebyteBuilder
    {
        $SynclogSql = $this->model::query();

        $SelectSql = 'date_end';

        $SynclogSql->selectRaw($SelectSql);

        $this->ApplyFilters($SynclogSql, $Filter);

        return $SynclogSql->orderByDesc('date_end')->first();
    }

    protected function ApplyFilters(Builder $Builder, SyncLogFilter $Filter)
    {
        if (isset($Filter->model)) {
            //get the last completed sync for the specified model
            $Builder->where($this->model::TABLE . '.' . $this->model::CONFIG, $Filter->model)
                ->whereNotNull($this->model::TABLE . '.' . $this->model::DATE_END);
        }
    }
}
