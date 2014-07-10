<?php namespace Orchestra\Tenanti;

use InvalidArgumentException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migrator;
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

    protected $resolver = array(
        'repository' => 'Illuminate\Database\Migrations\DatabaseMigrationRepository',
        'migrator'   => 'Illuminate\Database\Migrations\Migrator',
    );

    /**
     * Set tenant configuration.
     *
     * @param  \Illuminate\Container\Container  $app
     * @param  array                            $config
     * @return MigratorFactory
     */
    public function __construct(Container $app, $driver, array $config = array())
    {
        $this->app    = $app;
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * Run migrations.
     *
     * @param  bool     $pretend
     * @return void
     */
    public function run($pretend = false)
    {
        $model = $this->resolveModel();
        $me    = $this;

        $model->newQuery()->chunk(100, function ($entities) use ($me, $pretend) {
            foreach ($entities as $entity) {
                $me->runUp($entity, $pretend);
            }
        });
    }

    /**
     * Rollback migrations.
     *
     * @return void
     */
    public function rollback($pretend = false)
    {
        $model = $this->resolveModel();
        $me    = $this;

        $model->newQuery()->chunk(100, function ($entities) use ($me, $pretend) {
            foreach ($entities as $entity) {
                $me->runDown($entity, $pretend);
            }
        });
    }

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  bool                                 $pretend
     * @return void
     */
    public function runUp(Model $entity, $pretend = false)
    {
        $table = $this->resolveTableName($entity);

        $this->resolveMigrator($table)->run(array_get($this->config, 'path'), $pretend);
    }

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  bool                                 $pretend
     * @return void
     */
    public function runDown(Model $entity, $pretend = false)
    {
        $table = $this->resolveTableName($entity);

        $this->resolveMigrator($table)->rollback($this->getMigrationPath(), $pretend);
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
