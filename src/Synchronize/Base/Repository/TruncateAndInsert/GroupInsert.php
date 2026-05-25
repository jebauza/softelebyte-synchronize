<?php


namespace Softelebyte\Synchronize\Base\Repository\TruncateAndInsert;


use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Softelebyte\Synchronize\Base\Contracts\Repo\InsertContact;
use Softelebyte\Synchronize\Base\Contracts\ValueObjects\AdditionalQueryChange;
use Softelebyte\Synchronize\Base\Models\Model;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupTruncateValueObject;

class GroupInsert implements InsertContact
{
    protected Model $targetModel;
    protected Model $originModel;
    protected GroupTruncateValueObject $object;

    public function __construct(GroupTruncateValueObject $object)
    {
        $this->object = $object;
        $this->targetModel = resolve($object->targetModel());
        $this->originModel = resolve($object->originModel());
    }

    public function insert(): void
    {
        $queryOrigin = $this->principalQuery();

        DB::table($this->targetModel->getCompleteTableName())
            ->insertUsing(
                $this->object->insert(),
                $queryOrigin
            );
    }

    protected function principalQuery(): Builder
    {
        $queryOrigin = DB::table($this->originModel->getCompleteTableName(), $this->object->originName())
            ->select($this->object->select());
        if ($this->object instanceof AdditionalQueryChange) {
            $this->object->additionalQueryChange($queryOrigin);
        }
        return $queryOrigin;
    }

    public function getOriginCount(): int
    {
        $queryOrigin = $this->principalQuery();
        $query = DB::query()->fromSub($queryOrigin, $this->object->originName());
        return $query->count();
    }
}