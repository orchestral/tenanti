<?php namespace Orchestra\Tenanti\Migrator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Container\Container;

class Factory implements FactoryInterface
{
    use NotableTrait, OperationTrait;

    /**
     * Chunk value.
     *
     * @var int
     */
    protected $chunk = 100;

    /**
     * Construct a new migration manager.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  string  $driver
     * @param  array  $config
     * @param  int  $chunk
     */
    public function __construct(Container $app, $driver, array $config = [], $chunk = 100)
    {
        $this->app    = $app;
        $this->driver = $driver;
        $this->config = $config;
        $this->chunk  = $chunk;
    }

    /**
     * Install migrations.
     *
     * @param  string|null  $database
     *
     * @return void
     */
    public function install($database)
    {
        $model = $this->getModel();

        $model->newQuery()->chunk($this->chunk, function ($entities) use ($database) {
            foreach ($entities as $entity) {
                $this->runInstall($entity, $database);
            }
        });
    }

    /**
     * Run migrations.
     *
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function run($database, $pretend = false)
    {
        $model = $this->getModel();

        $model->newQuery()->chunk($this->chunk, function ($entities) use ($database, $pretend) {
            foreach ($entities as $entity) {
                $this->runUp($entity, $database, $pretend);
            }
        });
    }

    /**
     * Rollback migrations.
     *
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function rollback($database, $pretend = false)
    {
        $model = $this->getModel();

        $model->newQuery()->chunk($this->chunk, function ($entities) use ($database, $pretend) {
            foreach ($entities as $entity) {
                $this->runDown($entity, $database, $pretend);
            }
        });
    }

    /**
     * Reset migrations.
     *
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function reset($database, $pretend = false)
    {
        $model = $this->getModel();

        $model->newQuery()->chunk($this->chunk, function ($entities) use ($database, $pretend) {
            foreach ($entities as $entity) {
                $this->runReset($entity, $database, $pretend);
            }
        });
    }

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     *
     * @return void
     */
    public function runInstall(Model $entity, $database)
    {
        $database = $this->bindWithKey($entity, $database);
        $table    = $this->resolveMigrationTableName($entity);

        $repository = $this->resolveMigrator($table)->getRepository();

        $repository->setSource($database);

        if (! $repository->repositoryExists()) {
            $repository->createRepository();

            $this->note("<info>Migration table {$table} created successfully.</info>");
        }
    }

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runUp(Model $entity, $database, $pretend = false)
    {
        $database = $this->bindWithKey($entity, $database);
        $table    = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigrator($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);
        $migrator->run($this->getMigrationPath(), $pretend);

        $this->mergeMigratorNotes($migrator);
    }

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runDown(Model $entity, $database, $pretend = false)
    {
        $database = $this->bindWithKey($entity, $database);
        $table    = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigrator($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);
        $migrator->rollback($pretend);

        $this->mergeMigratorNotes($migrator);
    }

    /**
     * Run migration reset on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runReset(Model $entity, $database, $pretend = false)
    {
        $database = $this->bindWithKey($entity, $database);
        $table    = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigrator($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);

        do {
            $count = $migrator->rollback($pretend);
        } while ($count > 0);

        $this->mergeMigratorNotes($migrator);
    }
}
