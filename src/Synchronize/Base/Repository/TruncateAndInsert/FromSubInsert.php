<?php


namespace Softelebyte\Synchronize\Base\Repository\TruncateAndInsert;


use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertContact;
use Softelebyte\Synchronize\Base\Models\Model;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupTruncateSubValueObject;

class FromSubInsert implements InsertContact
{
    protected Model $targetModel;
    protected GroupTruncateSubValueObject $object;

    public function __construct(GroupTruncateSubValueObject $object)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
    }

    public function insert(): void
    {
        $queryDestiny = $this->object->subFromQuery();
        DB::table($this->targetModel->getCompleteTableName())
            ->insertUsing(
                $this->object->insert(),
                $queryDestiny
            );
    }

    public function getOriginCount(): int
    {
        $query = DB::query()->fromSub($this->object->subFromQuery(), $this->object->originName());
        return $query->count();
    }
}