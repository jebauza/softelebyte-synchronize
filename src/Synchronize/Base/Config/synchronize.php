<?php

use Softelebyte\Synchronize\Base\Decorator\FromSubDecorator;
use Softelebyte\Synchronize\Base\Decorator\MultiLevelRepoDecorator;
use Softelebyte\Synchronize\Base\Decorator\RepoBeDecorator;
use Softelebyte\Synchronize\Base\Decorator\RepoDecorator;
use Softelebyte\Synchronize\Base\Decorator\RepoNotDeleteDecorator;
use Softelebyte\Synchronize\Base\Decorator\SimpleDecorator;
use Softelebyte\Synchronize\Base\Decorator\SqlServer\GroupInsertAndUpdateDecorator;
use Softelebyte\Synchronize\Base\Decorator\SqlServer\GroupSubTruncateDecorator;
use Softelebyte\Synchronize\Base\Decorator\SqlServer\GroupTruncateDecorator;
use Softelebyte\Synchronize\Base\Decorator\SqlServer\SimpleDecorator as SimpleDecoratorSqlServer;
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;
use Softelebyte\Synchronize\Base\Models\BaseModel;
use Softelebyte\Synchronize\Base\ValueObjects\Service\FromSubTableValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\MultiParentRepoValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\RepoBeValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\RepoNotDelete;
use Softelebyte\Synchronize\Base\ValueObjects\Service\RepoValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupTruncateSubValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupTruncateValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\GroupValueObject;
use Softelebyte\Synchronize\Base\ValueObjects\Service\ServiceNotDuplicateKey\SimpleTableValueObject as SimpleTableValueObjectNotDuplicateKey;
use Softelebyte\Synchronize\Base\ValueObjects\Service\SimpleTableValueObject;

return [
    'default_group' => 'default_class',
    'alias' => [
    ],
    'services' => [
        RepoNotDelete::class => RepoNotDeleteDecorator::class,
        RepoValueObject::class => RepoDecorator::class,
        RepoBeValueObject::class => RepoBeDecorator::class,
        MultiParentRepoValueObject::class => MultiLevelRepoDecorator::class,
        SimpleTableValueObject::class => SimpleDecorator::class,
        FromSubTableValueObject::class => FromSubDecorator::class,
        GroupValueObject::class => GroupInsertAndUpdateDecorator::class,
        GroupTruncateValueObject::class => GroupTruncateDecorator::class,
        GroupTruncateSubValueObject::class => GroupSubTruncateDecorator::class,
        SimpleTableValueObjectNotDuplicateKey::class => SimpleDecoratorSqlServer::class,
    ],
    'mysql_placeholder_max' => env('MYSQL_PLACEHOLDER_MAX', 65535),
    'active_table_etl_minimum_days' => env('ACTIVE_TABLE_ETL_MINIMUM_DAYS', 30),
    'active_table_minimum_days' => env('ACTIVE_TABLE_MINIMUM_DAYS', 15),
    'error_email_receptor' => env('ERROR_MAIL_RECEPTOR', null),
    'maxLogQuery' => env('MAX_LOG_QUERY', 1),
    'active_etl' => BaseEtlModel::ACTIVE_ETL,
    'active_be' => BaseModel::ACTIVE_BE,
    'logSql' => 'mysql',
    'seeders' => [
    ]
];
