<?php namespace Orchestra\Tenanti\Migrator;

use Closure;
use Orchestra\Support\Str;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

trait OperationTrait
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
     * @var array
     */
    protected $config = [];

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
     * Resolve model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \InvalidArgumentException
     */
    public function getModel()
    {
        $name  = $this->getModelName();
        $model = $this->app->make($name);

        if (! $model instanceof Model) {
            throw new InvalidArgumentException("Model [{$name}] should be an instance of Eloquent.");
        }

        $database = Arr::get($this->config, 'database');

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
        $app      = $this->app;
        $resolver = $this->resolver;

        if (! isset($this->migrator[$table])) {
            $repository = $app->make(Arr::get($resolver, 'repository'), [$app['db'], $table]);
            $migrator   = $app->make(Arr::get($resolver, 'migrator'), [$repository, $app['db'], $app['files']]);

            $this->migrator[$table] = $migrator;
        }

        return $this->migrator[$table];
    }

    /**
     * Set tenant as default database connection.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string  $database
     *
     * @return string
     */
    public function asDefaultDatabase(Model $entity, $database)
    {
        $connection = $this->resolveDatabaseConnection($entity, $database);

        $this->app->make('config')->set('database.default', $connection);

        return $connection;
    }

    /**
     * Resolve database connection.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string  $database
     *
     * @return string
     */
    protected function resolveDatabaseConnection(Model $entity, $database)
    {
        $repository = $this->app->make('config');
        $connection = $this->bindWithKey($entity, $database);
        $database   = Arr::get($this->config, 'database');
        $name       = "database.connections.{$connection}";

        if (! is_null($database) && is_null($repository->get($name))) {
            $config = $this->app->call($database['resolver'], [
                'entity'     => $entity,
                'template'   => $database['template'],
                'connection' => $connection,
            ]);

            $repository->set($name, $config);
        }

        return $connection;
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
        if (is_null($table = Arr::get($this->config, 'migration'))) {
            $table = $this->getTablePrefix().'_migrations';
        }

        return $this->bindWithKey($entity, $table);
    }

    /**
     * Get migration path.
     *
     * @return mixed
     */
    public function getMigrationPath()
    {
        return Arr::get($this->config, 'path');
    }

    /**
     * Get model name.
     *
     * @return mixed
     */
    public function getModelName()
    {
        return Arr::get($this->config, 'model');
    }

    /**
     * Get table prefix.
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return implode('_', [$this->driver, '{id}']);
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
        if (is_null($name)) {
            return $name;
        }

        $id = $entity->getKey();

        if (! isset($this->data[$id])) {
            $data       = Arr::dot(['entity' => $entity->toArray()]);
            $data['id'] = $id;

            $this->data[$id] = $data;
        }

        return Str::replace($name, $this->data[$id]);
    }
}
