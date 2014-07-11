<?php namespace Orchestra\Tenanti\Migrator;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;

class Factory implements FactoryInterface
{
    use OperationTrait;

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
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null                          $database
     * @return void
     */
    public function runInstall(Model $entity, $database)
    {
        $table = $this->resolveMigrationTableName($entity);

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
        $table    = $this->resolveMigrationTableName($entity);
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
        $table = $this->resolveMigrationTableName($entity);
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
        $table = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigrator($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);

        do {
            $count = $migrator->rollback($pretend);
        } while ($count > 0);
    }
}
