<?php


namespace Softelebyte\SoftelebyteJoins;


use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait SoftelebyteJoins
{
    /**
     * Cache to not join the same relationship twice.
     *
     * @var array
     */
    public $joinSoftelebyteRelationship = [];

    /**
     * Cache to not join the same relationship twice.
     *
     * @var string
     */
    public $softelebyteJoinAliases = [];


    /**
     * Join method map.
     */
    public $joinMethodsMap = [
        'join' => 'softelebyteJoin',
        'leftJoin' => 'leftSoftelebyteJoin',
        'rightJoin' => 'rightSoftelebyteJoin',
    ];

    /**
     * Join the relationship(s).
     */
    public function joinSoftelebyte($relationName, $callback = null, $joinType = 'join', $useAlias = false, bool $disableExtraConditions = false): self
    {
        $relationNameJoin = $relationName;
        $joinType = $this->joinMethodsMap[$joinType] ?? $joinType;
        if (Str::contains($relationName, '.')) {
            $this->joinSoftelebyteNestedRelationship($relationName, $callback, $joinType, $useAlias, $disableExtraConditions);
            return $this;
        }

        $relationQuery = $this->getModel()->{$relationName}()->getQuery();

        $relationModel = $relationQuery->getModel();
        $aliasCallback = $callback;
        if ($aliasCallback && is_array($aliasCallback) && isset($aliasCallback[$relationName])) {
            $aliasCallback = $aliasCallback[$relationName];
        }

        if (isset($aliasCallback) && is_callable($aliasCallback)) {
            if ($relationQuery instanceof BelongsToMany || $relationQuery instanceof HasManyThrough) {
                $arrayCallback = null;
                if (is_array($aliasCallback) && isset($aliasCallback[$relationModel->getTable()])) {
                    $arrayCallback = $aliasCallback[$relationModel->getTable()];
                }
                $aliasCallback = $arrayCallback;
            }
        }

        if (isset($aliasCallback)) {
            $join = new SoftelebyteJoinClause($this->getQuery(), $joinType, $relationModel->getTable(), $relationModel);
            $aliasCallback($join);
            if (isset($join->alias)) {
                $as[$relationModel->getTable()] = $join->alias;
                $relationNameJoin = $join->alias;
            }
        }

        $relationJoinCache = "{$relationQuery->getModel()->getTable()}.{$relationNameJoin}";

        if ($this->relationshipSoftelebyteAlreadyJoined($relationJoinCache)) {
            return $this;
        }

        if(isset($join) && isset($join->alias) && !$useAlias) {
            $this->cacheSoftelebyteJoinAlias($relationName, $as);
        }

        $relation = $this->getModel()->{$relationName}();
        $alias = $useAlias ? $this->generateAliasForRelationship($relation, $relationName) : null;
        if ($useAlias) {
            $as[$relationModel->getTable()] = $alias;
            $this->cacheSoftelebyteJoinAlias($relationName, $as);
        }
        $relation->performJoinForEloquentSoftelebyteJoins(
            $relationName,
            $this,
            $joinType,
            $callback,
            $alias,
            $disableExtraConditions
        );

        if ($useAlias) {
            $this->cacheSoftelebyteJoinAlias($relationName, $alias);
        }
        $this->markRelationshipSoftelebyteAsAlreadyJoined($relationJoinCache);
        return $this;
    }

    public function leftJoinSoftelebyte(
        $relation,
        $callback = null,
        $useAlias = false,
        bool $disableExtraConditions = false
    ): self
    {
        $this->joinSoftelebyte($relation, $callback, 'leftJoin', $useAlias, $disableExtraConditions);
        return $this;
    }

    public function joinSoftelebyteUsingAlias($relationName, $callback = null, bool $disableExtraConditions = false): self
    {
        $this->joinSoftelebyte($relationName, $callback, 'join', true, $disableExtraConditions);
        return $this;
    }

    public function leftJoinSoftelebyteUsingAlias($relationName, $callback = null, bool $disableExtraConditions = false): self
    {
        $this->joinSoftelebyte($relationName, $callback, 'leftJoin', true, $disableExtraConditions);
        return $this;
    }

    public function rightJoinSoftelebyte($relation, $callback = null, $useAlias = false, bool $disableExtraConditions = false): self
    {
        $this->joinSoftelebyte($relation, $callback, 'rightJoin', $useAlias, $disableExtraConditions);
        return $this;
    }

    public function rightJoinSoftelebyteUsingAlias($relationName, $callback = null, bool $disableExtraConditions = false): self
    {
        $this->joinSoftelebyte($relationName, $callback, 'rightJoin', true, $disableExtraConditions);
        return $this;
    }

    public function generateAliasForRelationship($relation, $relationName)
    {
        if ($relation instanceof BelongsToMany || $relation instanceof HasManyThrough) {
            return [
                md5($relationName . 'table1' . time()),
                md5($relationName . 'table2' . time()),
            ];
        }

        return md5($relationName . time());
    }

    /**
     * Marks the relationship as already joined.
     */
    public function markRelationshipSoftelebyteAsAlreadyJoined($relation)
    {
        $this->joinSoftelebyteRelationship[$relation] = true;
    }

    /**
     * Checks if the relationship was already joined.
     */
    public function relationshipSoftelebyteAlreadyJoined($relation)
    {
        return isset($this->joinSoftelebyteRelationship[$relation]);
    }

    /**
     * Cache the softelebyte join table alias used for the softelebyte join.
     * @param Model $relationModel
     * @param $alias
     */
    public function cacheSoftelebyteJoinAlias(string $relationName, $alias)
    {
        $this->softelebyteJoinAliases[$relationName] = $alias;
    }

    public function getSoftelebyteJoinAliases(string $relationName, $model)
    {
        return $this->softelebyteJoinAliases[$relationName][$model] ?? null;
    }

    /**
     * Join nested relationships.
     * @param $relations
     * @param null $callback
     * @param string $joinType
     * @param bool $useAlias
     * @param bool $disableExtraConditions
     */
    public function joinSoftelebyteNestedRelationship($relations, $callback = null, $joinType = 'join', $useAlias = false, bool $disableExtraConditions = false): void
    {
        $relations = explode('.', $relations);

        /** @var Relation */
        $latestRelation = null;
        $concatRelation = '';

        foreach ($relations as $relationName) {
            $concatRelation .= $relationName;
            $currentModel = $latestRelation ? $latestRelation->getModel() : $this->getModel();
            $relation = $currentModel->{$relationName}();
            $alias = $useAlias ? $this->generateAliasForRelationship($relation, $relationName) : null;
            $relationCallback = null;
            $relationModel = $relation->getQuery()->getModel();
            $relationJoinCache = $relationModel->getTable() . '.' . $concatRelation;

            $latestRelation = $relation;
            if ($this->relationshipSoftelebyteAlreadyJoined($relationJoinCache)) {
                $concatRelation .= '.';
                continue;
            }

            if ($useAlias) {
                $as = [];
                $joinAlias = $alias;
                if (!is_array($joinAlias)) {
                    $relationTable = $relationModel->getTable();
                    $joinAlias = [$joinAlias];
                }

                $as[$relationTable ?? $relation->getTable()] = $joinAlias[0];

                if (isset($joinAlias[1])) {
                    $as[$relationModel->getTable()] = $joinAlias[1];
                }

                $this->cacheSoftelebyteJoinAlias($concatRelation, $as);
            }

            if ($callback && is_array($callback) && isset($callback[$relationName])) {
                $relationCallback = $callback[$relationName];
                $aliasCallback = $relationCallback;
            }

            if (isset($aliasCallback)) {
                $arrayCallback = $aliasCallback;

                if (!is_array($aliasCallback)) {
                    $arrayCallback = [];
                    $arrayCallback[$relationModel->getTable()] = $aliasCallback;
                }

                $as = [];

                foreach ($arrayCallback as $model => $callbackJoin) {
                    $join = new SoftelebyteJoinClause($this->getQuery(), $joinType, $relationModel->getTable(), $relationModel);
                    $callbackJoin($join);
                    if (isset($join->alias)) {
                        $as[$model] = $join->alias;
                    }
                }

                if (!empty($as)) {
                    $this->cacheSoftelebyteJoinAlias($concatRelation, $as);
                }
            }

            $relation->performJoinForEloquentSoftelebyteJoins(
                $concatRelation,
                $this,
                $joinType,
                $relationCallback,
                $alias,
                $disableExtraConditions
            );

            $this->markRelationshipSoftelebyteAsAlreadyJoined($relationJoinCache);

            $concatRelation .= '.';
        }
    }


    /**
     * Order by a field in the defined relationship.
     */
    public function orderBySoftelebyteJoins($sort, $direction = 'asc', $aggregation = null, $joinType = 'join'): self
    {
        $relationships = explode('.', $sort);
        $column = array_pop($relationships);
        $latestRelationshipName = $relationships[count($relationships) - 1];

        $this->joinSoftelebyte(implode('.', $relationships), null, $joinType);
        if (is_null($this->getSelect())) {
            $this->select(sprintf('%s.*', $this->getModel()->getTable()));
        }
        $latestRelationshipModel = array_reduce($relationships, function ($model, $relationshipName) {
            return $model->$relationshipName()->getModel();
        }, $this->getModel());

        if ($aggregation) {
            $this->selectRaw(
                sprintf(
                    '%s(%s.%s) as %s_aggregation',
                    $aggregation,
                    $latestRelationshipModel->getTable(),
                    $column,
                    $latestRelationshipName
                )
            )
                ->groupBy(sprintf('%s.%s', $this->getModel()->getTable(), $this->getModel()->getKeyName()))
                ->orderBy(sprintf('%s_aggregation', $latestRelationshipName), $direction);
        } else {
            $this->orderBy(sprintf('%s.%s', $latestRelationshipModel->getTable(), $column), $direction);
        }
        return $this;
    }

    public function orderByLeftSoftelebyteJoins($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, null, 'leftJoin');
        return $this;
    }

    /**
     * Order by the COUNT aggregation using joins.
     */
    public function orderBySoftelebyteJoinsCount($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'COUNT');
        return $this;
    }

    public function orderByLeftSoftelebyteJoinsCount($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'COUNT', 'leftJoin');
        return $this;
    }

    /**
     * Order by the SUM aggregation using joins.
     */
    public function orderBySoftelebyteJoinsSum($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'SUM');
        return $this;
    }

    public function orderByLeftSoftelebyteJoinsSum($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'SUM', 'leftJoin');
        return $this;
    }

    /**
     * Order by the AVG aggregation using joins.
     */
    public function orderBySoftelebyteJoinsAvg($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'AVG');
        return $this;
    }

    public function orderByLeftSoftelebyteJoinsAvg($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'AVG', 'leftJoin');
        return $this;
    }

    /**
     * Order by the MIN aggregation using joins.
     */
    public function orderBySoftelebyteJoinsMin($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'MIN');
        return $this;
    }

    public function orderByLeftSoftelebyteJoinsMin($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'MIN', 'leftJoin');
        return $this;
    }

    /**
     * Order by the MAX aggregation using joins.
     */
    public function orderBySoftelebyteJoinsMax($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'MAX');
        return $this;
    }

    public function orderByLeftSoftelebyteJoinsMax($sort, $direction = 'asc'): self
    {
        $this->orderBySoftelebyteJoins($sort, $direction, 'MAX', 'leftJoin');
        return $this;
    }


    public function softelebyteJoinsHas(
        $relation,
        $operator = '>=',
        $count = 1,
        $boolean = 'and',
        Closure $callback = null
    ): self
    {
        $relationName = $relation;

        if (is_null($this->getSelect())) {
            $this->select(sprintf('%s.*', $this->getModel()->getTable()));
        }

        if (is_null($this->getGroupBy())) {
            $this->groupBy($this->getModel()->getQualifiedKeyName());
        }

        if (is_string($relation)) {
            if (Str::contains($relation, '.')) {
                $this->hasNestedUsingJoins($relation, $operator, $count, 'and', $callback);

                return $this;
            }

            $relation = $this->getRelationWithoutConstraintsProxy($relation);
        }

        $relation->performJoinForEloquentSoftelebyteJoins($relationName, $this, 'leftSoftelebyteJoin', $callback);
        $relation->performHavingForEloquentSoftelebyteJoins($this, $operator, $count);
        return $this;
    }

    public function hasNestedUsingJoins(
        $relations,
        $operator = '>=',
        $count = 1,
        $boolean = 'and',
        Closure $callback = null
    )
    {
        $relations = explode('.', $relations);

        /** @var Relation */
        $latestRelation = null;
        $concatRelation = '';

        foreach ($relations as $index => $relation) {
            $concatRelation .= $relation;
            if (!$latestRelation) {
                $relation = $this->getRelationWithoutConstraintsProxy($relation);
            } else {
                $relation = $latestRelation->getModel()->query()->getRelationWithoutConstraintsProxy($relation);
            }

            $relation->performJoinForEloquentSoftelebyteJoins($concatRelation, $this, 'leftSoftelebyteJoin', $callback);

            if (count($relations) === ($index + 1)) {
                $relation->performHavingForEloquentSoftelebyteJoins($this, $operator, $count);
            }

            $latestRelation = $relation;
            $concatRelation .= '.';
        }
    }

    public function softelebyteJoinsDoesntHave($relation, $boolean = 'and', Closure $callback = null): self
    {
        $this->softelebyteJoinsHas($relation, '<', 1, $boolean, $callback);
        return $this;
    }

    public function softelebyteJoinsWhereHas($relation, Closure $callback = null, $operator = '>=', $count = 1): self
    {
        $this->softelebyteJoinsHas($relation, $operator, $count, 'and', $callback);
        return $this;
    }

}
