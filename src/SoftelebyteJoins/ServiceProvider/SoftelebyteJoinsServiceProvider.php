<?php

namespace Softelebyte\SoftelebyteJoins\ServiceProvider;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;
use Softelebyte\SoftelebyteJoins\Mixins\JoinRelationship;
use Softelebyte\SoftelebyteJoins\Mixins\QueryBuilderExtraMethods;
use Softelebyte\SoftelebyteJoins\Mixins\QueryRelationshipExistence;
use Softelebyte\SoftelebyteJoins\Mixins\RelationshipsExtraMethods;

class SoftelebyteJoinsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        EloquentQueryBuilder::mixin(new JoinRelationship());
        EloquentQueryBuilder::mixin(new QueryRelationshipExistence());
        QueryBuilder::mixin(new QueryBuilderExtraMethods());

        Relation::mixin(new RelationshipsExtraMethods());
    }
}