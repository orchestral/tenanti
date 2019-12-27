<?php

namespace Orchestra\Tenanti\Migrator;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Orchestra\Support\Str;
use Orchestra\Tenanti\Contracts\Notice;

trait Operation
{
    use Notable;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Tenant driver name.
     *
     * @var string
     */
    protected $driver;

    /**
     * Tenant manager.
     *
     * @var \Orchestra\Tenanti\TenantiManager
     */
    protected $manager;

    /**
     * Cached migrators.
     *
     * @var array
     */
    protected $migrator = [];

    /**
     * Cached entities data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Paths that will be added to the migration.
     *
     * @var array
     */
    protected $migrationPaths = [];

    /**
     * Resolver list.
     *
     * @var array
     */
    protected $resolver = [
        'repository' => DatabaseMigrationRepository::class,
        'migrator' => Migrator::class,
    ];

    /**
     * Execute query by id.
     *
     * @param  int|string  $id
     */
    public function executeFor($id, Closure $callback): void
    {
        $callback(
            $this->newQuery()->findOrFail($id)
        );
    }

    /**
     * Execute query via cursor.
     */
    public function executeForEach(Closure $callback): void
    {
        $this->newQuery()->cursor()->each(static function ($user) use ($callback) {
            $callback($user);
        });
    }

    /**
     * Get tenant configuration.
     *
     * @param  mixed  $default
     *
     * @return mixed
     */
    protected function config(string $key, $default = null)
    {
        return $this->manager->config("{$this->driver}.{$key}", $default);
    }

    /**
     * Resolve model.
     *
     * @throws \InvalidArgumentException
     */
    public function model(): Model
    {
        $name = $this->modelName();
        $model = $this->app->make($name);

        if (! $model instanceof Model) {
            throw new InvalidArgumentException("Model [{$name}] should be an instance of Eloquent.");
        }

        $database = $this->config('database');

        if (! \is_null($database)) {
            $model->setConnection($database);
        }

        $model->useWritePdo();

        return $model;
    }

    /**
     * Get Model as new query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return $this->model()->newQuery()->useWritePdo();
    }

    /**
     * Resolve migrator.
     */
    protected function migrator(string $table): Migrator
    {
        $app = $this->app;

        if (! isset($this->migrator[$table])) {
            $respositoryClass = $this->resolver['repository'];
            $migratorClass = $this->resolver['migrator'];

            $repository = new $respositoryClass($app['db'], $table);
            $migrator = new $migratorClass($repository, $app['db'], $app['files']);

            $this->migrator[$table] = $migrator;
        }

        return $this->migrator[$table];
    }

    /**
     * Set tenant as default database connection and get the connection name.
     */
    public function asDefaultConnection(Model $entity, ?string $database): ?string
    {
        $connection = $this->connectionName($entity, $database);

        $this->app->make('config')->set('database.default', $connection);

        return $connection;
    }

    /**
     * Set tenant database connection.
     */
    public function connectionName(Model $entity, ?string $database): ?string
    {
        $repository = $this->app->make('config');
        $tenants = $this->config('connection');

        if (! \is_null($tenants)) {
            $database = $tenants['name'];
        }

        if (\substr($database, -5) !== '_{id}' && $this->config('shared', true) === false) {
            $database .= '_{id}';
        }

        $connection = $this->normalize($entity, $database);
        $name = "database.connections.{$connection}";

        if (! \is_null($tenants) && \is_null($repository->get($name))) {
            $config = $this->app->call($tenants['resolver'], [
                'entity' => $entity,
                'template' => $tenants['template'],
                'connection' => $connection,
                'migrator' => $this,
            ]);

            $repository->set($name, $config);
        }

        return $connection;
    }

    /**
     * Resolve tenant database connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public function connection(Model $entity, string $database)
    {
        return $this->app->make('db')->connection($this->connectionName($entity, $database));
    }

    /**
     * Get table name.
     */
    protected function migrationTableName(Model $entity): string
    {
        if (! \is_null($table = $this->config('migration'))) {
            return $this->normalize($entity, $table);
        }

        if ($this->config('shared', true) === true) {
            return $this->normalize($entity, $this->tablePrefix().'_migrations');
        }

        return 'tenant_migrations';
    }

    /**
     * Get model name.
     */
    public function modelName(): string
    {
        return $this->config('model');
    }

    /**
     * Get table prefix.
     */
    public function tablePrefix(): string
    {
        $prefix = $this->config('prefix', $this->driver);

        return \implode('_', [$prefix, '{id}']);
    }

    /**
     * Resolve table name.
     */
    protected function normalize(Model $entity, ?string $name): ?string
    {
        if (\is_null($name) || (\strpos($name, '{') === false && \strpos($name, '}') === false)) {
            return $name;
        }

        $id = $entity->getKey();

        if (! isset($this->data[$id])) {
            $data = \array_merge(Arr::dot(['entity' => $entity->toArray()]), \compact('id'));

            $this->data[$id] = $data;
        }

        return Str::replace($name, $this->data[$id]);
    }

    /**
     * Get default migration paths.
     */
    public function getDefaultMigrationPaths(): array
    {
        return Arr::wrap($this->config('paths', []));
    }

    /**
     * Get migration path.
     */
    public function getMigrationPaths(Model $entity = null): ?array
    {
        if (! \is_null($entity) && isset($this->migrationPaths[$entity->getKey()])) {
            return $this->migrationPaths[$entity->getKey()];
        }

        return $this->getDefaultMigrationPaths();
    }

    /**
     * Load migrations from a specific path.
     *
     * @param  string|array  $paths
     */
    public function loadMigrationsFrom($paths, Model $entity): void
    {
        $id = $entity->getKey();

        $migrations = \array_merge(
            ($this->migrationPaths[$id] ?? $this->getDefaultMigrationPaths()), Arr::wrap($paths)
        );

        $this->migrationPaths[$id] = \array_unique($migrations);
    }
}
