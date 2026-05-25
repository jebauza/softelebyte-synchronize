# softelebyte-synchronize

Unified Laravel package by Softelebyte for ETL data synchronization.

Combines synchronization engine, extended query builder, Eloquent relationship joins, binary UUID migrations, query filters, and console utilities into a single installable package.

## Requirements

- PHP ^8.0
- Laravel ^10.0

## Installation

### 1. Add the repository to your project's `composer.json`

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/YOUR_USERNAME/softelebyte-synchronize"
    }
]
```

### 2. Install the package

```bash
composer require softelebyte/softelebyte-synchronize
```

Laravel auto-discovers and registers all Service Providers.

### 3. Publish the configuration

```bash
php artisan vendor:publish --tag=synchronize-config
```

This creates `config/synchronize.php` in your project.

### 4. Run the migrations

```bash
php artisan migrate
```

Creates the log tables: `syncs`, `sync_logs`, `sync_errors`, `sync_last_config`, `sync_statuses`.

---

## Included Modules

| Namespace | Description |
|---|---|
| `Softelebyte\Synchronize\` | Core ETL synchronization engine |
| `Softelebyte\Builder\` | Extended Eloquent query builder |
| `Softelebyte\SoftelebyteJoins\` | Joins via Eloquent relationships |
| `Softelebyte\MigrationBinaryUuid\` | Binary UUID migration support |
| `Softelebyte\OutputHelper\` | Console output helper |
| `Softelebyte\SelectHelper\` | SQL select builder helper |
| `Softelebyte\QueryFilters\` | Reusable query filters |
| `Softelebyte\GoogleAnalytics\` | Google Analytics Data API connector |
| `Softelebyte\Stubs\` | Stub-based file generator |

---

## Usage

### Artisan Commands

```bash
# Run all synchronizations
php artisan synchronize:data

# Run a specific group
php artisan synchronize:data --group=group_name

# Run synchronization for a specific time
php artisan synchronize:data --synchour=08:00

# Print available group configuration
php artisan synchronize:print_config
```

### Table-to-Table Synchronization

```php
use Softelebyte\Synchronize\Base\ValueObjects\Service\SimpleTableValueObject;

class MySync extends SimpleTableValueObject
{
    public function originModel(): string
    {
        return SourceModel::class;
    }

    public function targetModel(): string
    {
        return TargetModel::class;
    }

    public function select(): array
    {
        return ['id', 'name', 'email'];
    }

    public function selectInsert(): array
    {
        return ['id', 'name', 'email'];
    }

    public function updateColumns(): array
    {
        return ['name', 'email'];
    }
}
```

### Custom Repository Synchronization

```php
use Softelebyte\Synchronize\Base\ValueObjects\Service\RepoValueObject;

class MyApiSync extends RepoValueObject
{
    public function targetModel(): string
    {
        return TargetModel::class;
    }

    public function getAll(): iterable
    {
        // Pull data from an external API, file, another database, etc.
        return ExternalApi::getData();
    }
}
```

### Registering a Sync Group

```php
use Softelebyte\Synchronize\Base\Contracts\Row\GroupRowClass;
use Softelebyte\Synchronize\Base\Contracts\Row\RowClass;

class MySyncGroup implements GroupRowClass
{
    public function groupRowClass(): array
    {
        return [
            new class implements RowClass {
                public function rowClass(): array
                {
                    return [
                        MySync::class,
                        MyApiSync::class,
                    ];
                }
            }
        ];
    }
}
```

Register in `config/synchronize.php`:

```php
'default_group' => 'my_group',
'alias' => [
    'my_group' => MySyncGroup::class,
],
```

---

## Configuration (`config/synchronize.php`)

| Key | Environment Variable | Description |
|---|---|---|
| `mysql_placeholder_max` | `MYSQL_PLACEHOLDER_MAX` | Max placeholders per query (default: 65535) |
| `active_table_etl_minimum_days` | `ACTIVE_TABLE_ETL_MINIMUM_DAYS` | Days before deleting inactive ETL records (default: 30) |
| `active_table_minimum_days` | `ACTIVE_TABLE_MINIMUM_DAYS` | Days before deleting inactive BE records (default: 15) |
| `error_email_receptor` | `ERROR_MAIL_RECEPTOR` | Email address for error notifications |
| `maxLogQuery` | `MAX_LOG_QUERY` | Max log entries per query (default: 1) |

---

## Base Models

```php
// Model with active_etl field (ETL synchronization)
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;

class MyModel extends BaseEtlModel {}

// Model with active_be field (backend synchronization)
use Softelebyte\Synchronize\Base\Models\BaseModel;

class MyModel extends BaseModel {}
```

Both automatically apply a global scope that filters out inactive records.

---

## Relationship Joins (SoftelebyteJoins)

```php
User::query()->joinSoftelebyte('posts')->get();
User::query()->leftJoinSoftelebyte('posts.comments')->get();
```

---

## Query Filters

```php
use Softelebyte\QueryFilters\Filters\QueryFilter;

class NameFilter extends QueryFilter
{
    public function name(string $value): void
    {
        $this->builder->where('name', 'like', "%$value%");
    }
}
```

```php
use Softelebyte\QueryFilters\Concerns\HasQueryFilters;

class MyModel extends Model
{
    use HasQueryFilters;
}

// In the controller
MyModel::filter($request, [NameFilter::class])->get();
```

---

## Auto-Registered Service Providers

- `Softelebyte\Synchronize\Base\Providers\SynchronizeDataServiceProvider`
- `Softelebyte\Synchronize\Logs\Providers\LogServiceProvider`
- `Softelebyte\SoftelebyteJoins\ServiceProvider\SoftelebyteJoinsServiceProvider`
- `Softelebyte\MigrationBinaryUuid\ServiceProvider\SoftelebyteBluePrintServiceProvider`
- `Softelebyte\OutputHelper\ServiceProvider\OutputHelperServiceProvider`
- `Softelebyte\QueryFilters\QueryFiltersServiceProvider`

---

## License

MIT © Softelebyte
