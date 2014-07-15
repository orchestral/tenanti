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
     * Resolve model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \InvalidArgumentException
     */
    public function getModel()
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

        if (is_null($table = array_get($this->config, 'migration'))) {
            $table = $this->getTablePrefix().'_migrations';
        }

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
