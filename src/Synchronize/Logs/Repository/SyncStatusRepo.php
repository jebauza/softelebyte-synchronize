<?php


namespace Softelebyte\Synchronize\Logs\Repository;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\MySqlConnection;
use Softelebyte\Synchronize\Logs\Entities\Binary\SyncStatus as BinarySyncStatus;
use Softelebyte\Synchronize\Logs\Entities\SyncStatus;

class SyncStatusRepo
{
    /**
     * @var SyncStatus Modelo
     */
    protected string $model = SyncStatus::class;

    public function __construct()
    {
        if((DB::connection() instanceof MySqlConnection)) {
            $this->model = BinarySyncStatus::class;
        }
    }

    public static function GetStatus(): Collection
    {
        $model = SyncStatus::class;
        if((DB::connection() instanceof MySqlConnection)) {
            $model = BinarySyncStatus::class;
        }
        return $model::query()->get();
    }

}
