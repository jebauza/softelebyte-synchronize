# softelebyte-synchronize

Paquete unificado de Softelebyte para sincronización ETL de datos en proyectos Laravel.

Integra en un solo paquete instalable los módulos de sincronización, query builder, joins por relaciones, migraciones con UUID binario, filtros de queries y utilidades de consola.

## Requisitos

- PHP ^8.0
- Laravel ^10.0

## Instalación

### 1. Agregar el repositorio en `composer.json` de tu proyecto

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/TU_USUARIO/softelebyte-synchronize"
    }
]
```

### 2. Instalar el paquete

```bash
composer require softelebyte/softelebyte-synchronize
```

Laravel registra los Service Providers automáticamente vía Package Discovery.

### 3. Publicar la configuración

```bash
php artisan vendor:publish --tag=synchronize-config
```

Esto crea el archivo `config/synchronize.php` en tu proyecto.

### 4. Ejecutar las migraciones

```bash
php artisan migrate
```

Crea las tablas de logs: `syncs`, `sync_logs`, `sync_errors`, `sync_last_config`, `sync_statuses`.

---

## Módulos incluidos

| Namespace | Descripción |
|---|---|
| `Softelebyte\Synchronize\` | Motor ETL principal |
| `Softelebyte\Builder\` | Query builder extendido |
| `Softelebyte\SoftelebyteJoins\` | Joins mediante relaciones Eloquent |
| `Softelebyte\MigrationBinaryUuid\` | Migraciones con UUID en binario |
| `Softelebyte\OutputHelper\` | Helper de salida en consola |
| `Softelebyte\SelectHelper\` | Helper para construcción de selects |
| `Softelebyte\QueryFilters\` | Filtros de queries reutilizables |
| `Softelebyte\GoogleAnalytics\` | Conector Google Analytics Data API |
| `Softelebyte\Stubs\` | Generador de archivos desde stubs |

---

## Uso del módulo ETL (Synchronize)

### Comando Artisan

```bash
# Ejecutar todas las sincronizaciones
php artisan synchronize:data

# Ejecutar un grupo específico
php artisan synchronize:data --group=nombre_grupo

# Ejecutar sincronización para una hora específica
php artisan synchronize:data --synchour=08:00

# Ver la configuración de grupos disponibles
php artisan synchronize:print_config
```

### Crear una sincronización tabla → tabla

```php
use Softelebyte\Synchronize\Base\ValueObjects\Service\SimpleTableValueObject;

class MiSincronizacion extends SimpleTableValueObject
{
    public function originModel(): string
    {
        return ModelOrigen::class;
    }

    public function targetModel(): string
    {
        return ModelDestino::class;
    }

    public function select(): array
    {
        return ['id', 'nombre', 'email'];
    }

    public function selectInsert(): array
    {
        return ['id', 'nombre', 'email'];
    }

    public function updateColumns(): array
    {
        return ['nombre', 'email'];
    }
}
```

### Crear una sincronización con repositorio personalizado

```php
use Softelebyte\Synchronize\Base\ValueObjects\Service\RepoValueObject;

class MiSincronizacionRepo extends RepoValueObject
{
    public function targetModel(): string
    {
        return ModelDestino::class;
    }

    public function getAll(): iterable
    {
        // Obtener datos desde API, archivo, otra DB, etc.
        return ApiExterna::getData();
    }
}
```

### Registrar el grupo de sincronización

```php
use Softelebyte\Synchronize\Base\Contracts\Row\GroupRowClass;
use Softelebyte\Synchronize\Base\Contracts\Row\RowClass;

class MiGrupoSincronizacion implements GroupRowClass
{
    public function groupRowClass(): array
    {
        return [
            new class implements RowClass {
                public function rowClass(): array
                {
                    return [
                        MiSincronizacion::class,
                        MiSincronizacionRepo::class,
                    ];
                }
            }
        ];
    }
}
```

Registrar en `config/synchronize.php`:

```php
'default_group' => 'mi_grupo',
'alias' => [
    'mi_grupo' => MiGrupoSincronizacion::class,
],
```

---

## Configuración (`config/synchronize.php`)

| Clave | Variable de entorno | Descripción |
|---|---|---|
| `mysql_placeholder_max` | `MYSQL_PLACEHOLDER_MAX` | Máximo de placeholders por query (default: 65535) |
| `active_table_etl_minimum_days` | `ACTIVE_TABLE_ETL_MINIMUM_DAYS` | Días mínimos antes de eliminar registros inactivos ETL (default: 30) |
| `active_table_minimum_days` | `ACTIVE_TABLE_MINIMUM_DAYS` | Días mínimos antes de eliminar registros inactivos BE (default: 15) |
| `error_email_receptor` | `ERROR_MAIL_RECEPTOR` | Email donde se envían notificaciones de error |
| `maxLogQuery` | `MAX_LOG_QUERY` | Número máximo de logs por query (default: 1) |

---

## Modelos base

```php
// Modelo con campo active_etl (sincronización ETL)
use Softelebyte\Synchronize\Base\Models\BaseEtlModel;

class MiModelo extends BaseEtlModel {}

// Modelo con campo active_be (sincronización backend)
use Softelebyte\Synchronize\Base\Models\BaseModel;

class MiModelo extends BaseModel {}
```

Ambos aplican automáticamente un scope global que filtra registros inactivos.

---

## Joins por relaciones (SoftelebyteJoins)

```php
// En tu modelo, aplica el trait a través del Builder
User::query()->joinSoftelebyte('posts')->get();
User::query()->leftJoinSoftelebyte('posts.comments')->get();
```

---

## Filtros de queries (QueryFilters)

```php
use Softelebyte\QueryFilters\Filters\QueryFilter;

class FiltroNombre extends QueryFilter
{
    public function nombre(string $valor): void
    {
        $this->builder->where('nombre', 'like', "%$valor%");
    }
}
```

```php
// En el modelo
use Softelebyte\QueryFilters\Concerns\HasQueryFilters;

class MiModelo extends Model
{
    use HasQueryFilters;
}

// En el controlador
MiModelo::filter($request, [FiltroNombre::class])->get();
```

---

## Service Providers registrados automáticamente

- `Softelebyte\Synchronize\Base\Providers\SynchronizeDataServiceProvider`
- `Softelebyte\Synchronize\Logs\Providers\LogServiceProvider`
- `Softelebyte\SoftelebyteJoins\ServiceProvider\SoftelebyteJoinsServiceProvider`
- `Softelebyte\MigrationBinaryUuid\ServiceProvider\SoftelebyteBluePrintServiceProvider`
- `Softelebyte\OutputHelper\ServiceProvider\OutputHelperServiceProvider`
- `Softelebyte\QueryFilters\QueryFiltersServiceProvider`

---

## Licencia

MIT © Softelebyte
