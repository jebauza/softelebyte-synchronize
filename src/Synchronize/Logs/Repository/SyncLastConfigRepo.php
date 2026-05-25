<?php


namespace Softelebyte\Synchronize\Logs\Repository;


use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Logs\DTO\SyncLastConfigDto;
use Softelebyte\Synchronize\Logs\Entities\Binary\SyncLastConfig as BinarySyncLastConfig;
use Softelebyte\Synchronize\Logs\Entities\SyncLastConfig;

class SyncLastConfigRepo
{
    /**
     * @var SyncLastConfig modelo
     */
    protected string $model = SyncLastConfig::class;
    protected array $bindings;

    public function __construct()
    {
        if((DB::connection() instanceof MySqlConnection)) {
            $this->model = BinarySyncLastConfig::class;
        }
    }

    function insert(SyncLastConfigDto $syncLastConfigDTO): SyncLastConfigDto
    {
        $syncLogModel = new $this->model();
        $syncLogModel->{$this->model::CONFIG} = $syncLastConfigDTO->config;
        $syncLogModel->{$this->model::SYNC_LOG_ID} = $syncLastConfigDTO->syncLogId;
        $syncLogModel->save();

        return $syncLastConfigDTO;
    }

    function insertOrUpdate(SyncLastConfigDto $syncLastConfigDTO): SyncLastConfigDto
    {
        $this->model::updateOrCreate(
            [
                $this->model::CONFIG => $syncLastConfigDTO->config,
            ],
            [
                $this->model::SYNC_LOG_ID => $syncLastConfigDTO->syncLogId,
            ]
        );

        return $syncLastConfigDTO;
    }
}
