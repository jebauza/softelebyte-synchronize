<?php


namespace Softelebyte\Synchronize\Logs\Service;


use Illuminate\Support\LazyCollection;
use Softelebyte\Synchronize\Logs\Entities\Sync;
use Softelebyte\Synchronize\Logs\Entities\SyncLastConfig;
use Softelebyte\Synchronize\Logs\Entities\SyncLog;
use Softelebyte\Synchronize\Logs\Fields\LastSynchronizeDateFields;

class LastSynchronizeDateService
{
    /**
     * @var array
     */
    private array $filterClass;

    /**
     * LastSynchronizeDateService constructor.
     * @param array $filterClass
     */
    public function __construct(array $filterClass)
    {
        $this->filterClass = $filterClass;
    }

    public function handle(): LazyCollection
    {
        return SyncLastConfig::query()
            ->select(
                [
                    SyncLastConfig::TABLE . '.' . SyncLastConfig::CONFIG,
                    Sync::TABLE . '.' . Sync::SYNC_DATA . ' as ' . LastSynchronizeDateFields::DATE
                ]
            )
            ->joinSoftelebyte('syncLog.sync')
            ->whereIn(
                SyncLog::TABLE . '.' . SyncLog::CONFIG,
                $this->filterClass
            )
            ->cursor();
    }

}
