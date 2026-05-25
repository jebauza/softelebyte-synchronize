<?php


namespace Softelebyte\Synchronize\Logs\Repository;


use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Softelebyte\Synchronize\Logs\DTO\SyncErrorDTO;
use Softelebyte\Synchronize\Logs\Entities\Binary\SyncError as BinarySyncError;
use Softelebyte\Synchronize\Logs\Entities\SyncError;

class SyncErrorRepo
{
    /**
     * @var SyncError Modelo
     */
    protected string $model = SyncError::class;

    public function __construct()
    {
        if((DB::connection() instanceof MySqlConnection)) {
            $this->model = BinarySyncError::class;
        }
    }

    public function insert(SyncErrorDTO $syncErrorDTO): SyncErrorDTO
    {
        $syncModel = new $this->model();
        $syncModel->id = Uuid::uuid4()->toString();
        $syncModel->{$this->model::SYNC_ID} = $syncErrorDTO->SyncId;
        $syncModel->{$this->model::ERROR} = $syncErrorDTO->error;
        $syncModel->save();

        $syncErrorDTO->id = $syncModel->{SyncError::ID};

        return $syncErrorDTO;
    }
}
