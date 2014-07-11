<?php namespace Orchestra\Tenanti\Migrator;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Support\Str;

trait OperationTrait
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
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
    protected $config = array();

    /**
     * Cached migrators.
     *
     * @var array
     */
    protected $migrator = array();

    /**
     * Resolver list.
     *
     * @var array
     */
    protected $resolver = array(
        'repository' => 'Illuminate\Database\Migrations\DatabaseMigrationRepository',
        'migrator'   => 'Orchestra\Tenanti\Migrator\Migrator',
    );

    /**
     * Install migrations.
     *
     * @param  string|null  $database
     * @return void
     */
    public function install($database)
    {
        $model = $this->resolveModel();

        $model->newQuery()->chunk(100, function ($entities) use ($database) {
            foreach ($entities as $entity) {
                $this->runInstall($entity, $database);
            }
        });
    }

    /**
     * Run migrations.
     *
     * @param  string|null  $database
     * @param  bool         $pretend
     * @return void
     */
    public function run($database, $pretend = false)
    {
        $model = $this->resolveModel();

        $model->newQuery()->chunk(100, function ($entities) use ($database, $pretend) {
            foreach ($entities as $entity) {
                $this->runUp($entity, $database, $pretend);
            }
        });
    }

    /**
     * Rollback migrations.
     *
     * @param  string|null  $database
     * @param  bool         $pretend
     * @return void
     */
    public function rollback($database, $pretend = false)
    {
        $model = $this->resolveModel();

        $model->newQuery()->chunk(100, function ($entities) use ($database, $pretend) {
            foreach ($entities as $entity) {
                $this->runDown($entity, $database, $pretend);
            }
        });
    }

    /**
     * Reset migrations.
     *
     * @param  string|null  $database
     * @param  bool         $pretend
     * @return void
     */
    public function reset($database, $pretend = false)
    {
        $model = $this->resolveModel();

        $model->newQuery()->chunk(100, function ($entities) use ($database, $pretend) {
            foreach ($entities as $entity) {
                $this->runReset($entity, $database, $pretend);
            }
        });
    }

    /**
     * Resolve model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \InvalidArgumentException
     */
    protected function resolveModel()
    {
        $name  = $this->getModelName();
        $model = $this->app->make($name);

        if (! $model instanceof Model) {
            throw new InvalidArgumentException("Model [{$name}] should be an instance of Eloquent.");
        }

        return $model;
    }

    /**
     * Resolve migrator.
     *
     * @param  string   $table
     * @return \Orchestra\Tenanti\Migrator\Migrator
     */
    protected function resolveMigrator($table)
    {
        $app      = $this->app;
        $resolver = $this->resolver;

        if (! isset($this->migrator[$table])) {
            $repository = $app->make(array_get($resolver, 'repository'), array($app['db'], $table));
            $migrator   = $app->make(array_get($resolver, 'migrator'), array($repository, $app['db'], $app['files']));

            $this->migrator[$table] = $migrator;
        }

        return $this->migrator[$table];
    }

    /**
     * Get table name.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @return string
     */
    protected function resolveMigrationTableName(Model $entity)
    {
        $id    = $entity->getKey();
        $table = $this->getTablePrefix().'_migrations';

        return Str::replace($table, array('id' => $id));
    }

    /**
     * Get migration path.
     *
     * @return mixed
     */
    public function getMigrationPath()
    {
        return array_get($this->config, 'path');
    }

    /**
     * Get model name.
     *
     * @return mixed
     */
    public function getModelName()
    {
        return array_get($this->config, 'model');
    }

    /**
     * Get table prefix.
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return implode('_', array($this->driver, '{id}'));
    }
}
