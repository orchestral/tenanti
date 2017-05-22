<?php

namespace Orchestra\Tenanti\Migrator;

use Closure;
use Orchestra\Support\Str;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

trait Operation
{
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
     * Tenant configuration.
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
        'migrator'   => Migrator::class,
    ];

    /**
     * Execute query by id.
     *
     * @param  int|string  $id
     * @param  \Closure  $callback
     *
     * @return void
     */
    public function executeById($id, Closure $callback)
    {
        $entity = $this->newQuery()->findOrFail($id);

        return call_user_func($callback, $entity);
    }

    /**
     * Execute query via chunk.
     *
     * @param  \Closure  $callback
     *
     * @return void
     */
    public function executeByChunk(Closure $callback)
    {
        $this->newQuery()->chunk($this->chunk, $callback);
    }

    /**
     * Get tenant configuration.
     *
     * @param  string  $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        return $this->manager->getConfig("{$this->driver}.{$key}", $default);
    }

    /**
     * Resolve model.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        $name  = $this->getModelName();
        $model = $this->app->make($name);

        if (! $model instanceof Model) {
            throw new InvalidArgumentException("Model [{$name}] should be an instance of Eloquent.");
        }

        $database = $this->getConfig('database');

        if (! is_null($database)) {
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
        return $this->getModel()->newQuery();
    }

    /**
     * Resolve migrator.
     *
     * @param  string  $table
     *
     * @return \Orchestra\Tenanti\Migrator\Migrator
     */
    protected function resolveMigrator($table)
    {
        $app = $this->app;

        if (! isset($this->migrator[$table])) {
            $respositoryClass = Arr::get($this->resolver, 'repository');
            $migratorClass    = Arr::get($this->resolver, 'migrator');

            $repository = new $respositoryClass($app['db'], $table);
            $migrator   = new $migratorClass($repository, $app['db'], $app['files']);

            $this->migrator[$table] = $migrator;
        }

        return $this->migrator[$table];
    }

    /**
     * Set tenant as default database connection and get the connection name.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string  $database
     *
     * @return string
     */
    public function asDefaultConnection(Model $entity, $database)
    {
        $connection = $this->asConnection($entity, $database);

        $this->app->make('config')->set('database.default', $connection);

        return $connection;
    }

    /**
     * Set tenant database connection.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string  $database
     *
     * @return string
     */
    public function asConnection(Model $entity, $database)
    {
        $repository = $this->app->make('config');
        $tenants    = $this->getConfig('connection');

        if (! is_null($tenants)) {
            $database = $tenants['name'];
        }

        if (substr($database, -5) !== '_{id}' && $this->getConfig('shared', true) === false) {
            $database .= '_{id}';
        }

        $connection = $this->bindWithKey($entity, $database);
        $name       = "database.connections.{$connection}";

        if (! is_null($tenants) && is_null($repository->get($name))) {
            $config = $this->app->call($tenants['resolver'], [
                'entity'     => $entity,
                'template'   => $tenants['template'],
                'connection' => $connection,
                'migrator'   => $this,
            ]);

            $repository->set($name, $config);
        }

        return $connection;
    }

    /**
     * Resolve tenant database connection.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string  $database
     *
     * @return \Illuminate\Database\Connection
     */
    public function resolveConnection(Model $entity, $database)
    {
        return $this->app->make('db')->connection($this->asConnection($entity, $database));
    }

    /**
     * Get table name.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return string
     */
    protected function resolveMigrationTableName(Model $entity)
    {
        if (! is_null($table = $this->getConfig('migration'))) {
            return $this->bindWithKey($entity, $table);
        }

        if ($this->getConfig('shared', true) === true) {
            return $this->bindWithKey($entity, $this->getTablePrefix().'_migrations');
        }

        return 'tenant_migrations';
    }

    /**
     * Get migration path.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $entity
     *
     * @return string|array|null
     */
    public function getMigrationPath(Model $entity = null)
    {
        if ($entity !== null && isset($this->migrationPaths[$entity->getKey()])) {
            return array_merge([$this->getConfig('path')], $this->migrationPaths[$entity->getKey()]);
        }

        return $this->getConfig('path');
    }

    /**
     * Get model name.
     *
     * @return mixed
     */
    public function getModelName()
    {
        return $this->getConfig('model');
    }

    /**
     * Get table prefix.
     *
     * @return string
     */
    public function getTablePrefix()
    {
        $prefix = $this->getConfig('prefix', $this->driver);

        return implode('_', [$prefix, '{id}']);
    }

    /**
     * Resolve table name.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $name
     *
     * @return string|null
     */
    protected function bindWithKey(Model $entity, $name)
    {
        if (is_null($name) || (strpos($name, '{') === false && strpos($name, '}') === false)) {
            return $name;
        }

        $id = $entity->getKey();

        if (! isset($this->data[$id])) {
            $data = array_merge(Arr::dot(['entity' => $entity->toArray()]), compact('id'));

            $this->data[$id] = $data;
        }

        return Str::replace($name, $this->data[$id]);
    }

    /**
     * Load migrations from a specific path.
     *
     * @param  string|array  $paths
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return null
     */
    public function loadMigrationsFrom($paths, Model $entity)
    {
        $id = $entity->getKey();

        if (! isset($this->migrationPaths[$id])) {
            $this->migrationPaths[$id] = [];
        }

        $this->migrationPaths[$id] = array_merge($this->migrationPaths[$id], (array) $paths);
        $this->migrationPaths[$id] = array_unique($this->migrationPaths[$id]);
    }
}
