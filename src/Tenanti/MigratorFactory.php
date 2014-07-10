<?php namespace Orchestra\Tenanti;

use InvalidArgumentException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Support\Str;

class MigratorFactory implements MigratorFactoryInterface
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
        'migrator'   => 'Orchestra\Tenanti\Migrator',
    );

    /**
     * Construct a new migration manager.
     *
     * @param  \Illuminate\Container\Container  $app
     * @param  string                           $driver
     * @param  array                            $config
     */
    public function __construct(Container $app, $driver, array $config = array())
    {
        $this->app    = $app;
        $this->driver = $driver;
        $this->config = $config;
    }

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
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null                          $database
     * @return void
     */
    public function runInstall(Model $entity, $database)
    {
        $table = $this->resolveTableName($entity);

        $repository = $this->resolveMigrator($table)->getRepository();

        $repository->setSource($database);

        if (! $repository->repositoryExists()) {
            $repository->createRepository();
        }
    }

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null                          $database
     * @param  bool                                 $pretend
     * @return void
     */
    public function runUp(Model $entity, $database, $pretend = false)
    {
        $table    = $this->resolveTableName($entity);
        $migrator = $this->resolveMigrator($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);
        $migrator->run($this->getMigrationPath(), $pretend);
    }

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null                          $database
     * @param  bool                                 $pretend
     * @return void
     */
    public function runDown(Model $entity, $database, $pretend = false)
    {
        $table = $this->resolveTableName($entity);
        $migrator = $this->resolveMigrator($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);
        $migrator->rollback($pretend);
    }

    /**
     * Run migration reset on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null                          $database
     * @param  bool                                 $pretend
     * @return void
     */
    public function runReset(Model $entity, $database, $pretend = false)
    {
        $table = $this->resolveTableName($entity);
        $migrator = $this->resolveMigrator($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);

        while (true) {
            $count = $migrator->rollback($pretend);

            if ($count == 0) {
                break;
            }
        }
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
     * @return \Illuminate\Database\Migrations\Migrator
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
    protected function resolveTableName(Model $entity)
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
