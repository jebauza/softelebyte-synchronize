<?php


namespace Softelebyte\Synchronize\Base\Services\Table;


use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertContact;
use Softelebyte\Synchronize\Base\Contracts\Services\ServiceContract;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\InactiveQuery;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\TruncateObject;
use Softelebyte\Synchronize\Base\Helper\SqlServer\DropAndCreateForeignAndKey;
use Softelebyte\Synchronize\Base\Models\Model;

class TruncateAndInsert implements ServiceContract
{
    protected Model $targetModel;
    protected TruncateObject $object;
    protected InsertContact $insert;
    private DropAndCreateForeignAndKey $dropAndCreateForeignAndKey;

    public function __construct(TruncateObject $object, InsertContact $insert)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->insert = $insert;
        $this->dropAndCreateForeignAndKey = new DropAndCreateForeignAndKey($this->targetModel->getTable());
    }

    public function handle(): void
    {
        try {
            if ($this->targetModel->getConnection() instanceof SqlServerConnection) {
                $this->dropAndCreateForeignAndKey->dropIndex();
                $this->dropAndCreateForeignAndKey->dropForeign();
            }
            $this->truncate();
            $this->insert->insert();
        } finally {
            if ($this->targetModel->getConnection() instanceof SqlServerConnection) {
                $this->dropAndCreateForeignAndKey->createIndex();
                $this->dropAndCreateForeignAndKey->createForeignKeys();
            }
        }
    }

    protected function truncate(): void
    {
        DB::statement('TRUNCATE TABLE ' . $this->targetModel->getCompleteTableName());
    }

    public function getIsSameCount(): bool
    {
        $query = DB::table($this->targetModel->getCompleteTableName());
        if ($this->object instanceof InactiveQuery) {
            $query = $this->object->inactiveQuery($query);
        }
        $countDestiny = $query->count();
        $countOrigin = $this->insert->getOriginCount();
        return $countOrigin == $countDestiny;
    }
}