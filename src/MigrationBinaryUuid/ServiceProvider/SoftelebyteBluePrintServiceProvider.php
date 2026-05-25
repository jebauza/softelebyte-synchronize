<?php


namespace Softelebyte\MigrationBinaryUuid\ServiceProvider;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Connection;
use Illuminate\Database\Grammar;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;
use Softelebyte\MigrationBinaryUuid\Connection\MySqlConnection;
use Softelebyte\MigrationBinaryUuid\Connection\PostgresConnection;
use Softelebyte\MigrationBinaryUuid\Connection\SqlServerConnection;
use Softelebyte\MigrationBinaryUuid\Exceptions\UnknownGrammarClass;
use Softelebyte\MigrationBinaryUuid\Fields\GrammarFields;
use Softelebyte\MigrationBinaryUuid\Migrations\MigrationCreatorSoftelebyte;


class SoftelebyteBluePrintServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerConnections();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerCreator();
    }

    private function registerConnections(): void
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new MySqlConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new \Illuminate\Database\SQLiteConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('sqlsrv', function ($connection, $database, $prefix, $config) {
            return new SqlServerConnection($connection, $database, $prefix, $config);
        });

        $this->grammarTypeRealBinary();
        $this->grammarTypeRealUuid();
        $this->grammarTypeCreatedAt();
        $this->grammarTypeUpdatedAt();
    }

    private function grammarTypeRealBinary(): void
    {
        Grammar::macro('typeRealBinary', function (Fluent $column) {
            switch (class_basename(static::class)) {
                case GrammarFields::SQL_SERVER_GRAMMAR:
                case GrammarFields::MYSQL_GRAMMAR:
                    return sprintf('binary(%d)', $column->length ?? 16);

                case GrammarFields::POSTGRES_GRAMMAR:
                    return 'bytea';

                case GrammarFields::SQLITE_GRAMMAR:
                    return 'blob(256)';

                default:
                    throw new UnknownGrammarClass;
            }
        });
    }

    private function grammarTypeRealUuid(): void
    {
        Grammar::macro('typeRealUuid', function (Fluent $column) {
            switch (class_basename(static::class)) {
                case GrammarFields::MYSQL_GRAMMAR:
                    return 'binary(16)';

                case GrammarFields::POSTGRES_GRAMMAR:
                    return 'UUID';

                case GrammarFields::SQLITE_GRAMMAR:
                    return 'blob(256)';

                case GrammarFields::SQL_SERVER_GRAMMAR:
                    return 'uniqueidentifier';

                default:
                    throw new UnknownGrammarClass;
            }
        });
    }

    private function grammarTypeCreatedAt(): void
    {
        Grammar::macro('typeCreatedAt', function (Fluent $column) {
            switch (class_basename(static::class)) {
                case GrammarFields::SQLITE_GRAMMAR:
                case GrammarFields::MYSQL_GRAMMAR:
                    return 'DATETIME DEFAULT CURRENT_TIMESTAMP(0)';
                case GrammarFields::POSTGRES_GRAMMAR:
                    return 'timestamp without time zone DEFAULT CURRENT_TIMESTAMP';
                case GrammarFields::SQL_SERVER_GRAMMAR:
                    return 'DATETIME DEFAULT CURRENT_TIMESTAMP';

                default:
                    throw new UnknownGrammarClass;
            }
        });
    }

    private function grammarTypeUpdatedAt(): void
    {
        Grammar::macro('typeUpdatedAt', function (Fluent $column) {
            switch (class_basename(static::class)) {
                case GrammarFields::SQLITE_GRAMMAR:
                case GrammarFields::MYSQL_GRAMMAR:
                    return 'DATETIME DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0)';
                case GrammarFields::POSTGRES_GRAMMAR:
                    return 'timestamp without time zone DEFAULT CURRENT_TIMESTAMP';
                case GrammarFields::SQL_SERVER_GRAMMAR:
                    return 'DATETIME DEFAULT CURRENT_TIMESTAMP';

                default:
                    throw new UnknownGrammarClass;
            }
        });
    }

    protected function registerCreator()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new MigrationCreatorSoftelebyte($app['files'], $app->basePath('stubs'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [MigrationCreatorSoftelebyte::class];
    }
}
