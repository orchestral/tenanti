<?php

namespace Orchestra\Tenanti\Migrator;

use Illuminate\Support\Arr;
use Orchestra\Tenanti\Migration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migrator as BaseMigrator;

class Migrator extends BaseMigrator
{
    /**
     * Entity.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $entity;

    /**
     * The database default connection.
     *
     * @var string|null
     */
    protected $defaultConnection;

    /**
     * Set entity for migration.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return $this
     */
    public function setEntity(Model $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Set the default connection name.
     *
     * @param  string  $name
     *
     * @return void
     */
    public function setConnection($name)
    {
        if (! is_null($name)) {
            $this->defaultConnection = $this->resolver->getDefaultConnection();
        }

        parent::setConnection($name);
    }

    /**
     * Reset the default connection name.
     *
     * @param  string  $name
     *
     * @return void
     */
    public function resetConnection()
    {
        if (! is_null($this->defaultConnection)) {
            $this->resolver->setDefaultConnection($this->defaultConnection);
        }
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     *
     * @return object
     */
    public function resolve($file)
    {
        $class = parent::resolve($file);

        if ($class instanceof Migration) {
            $class->setConnection($this->connection);
        }

        return $class;
    }

    /**
     * Run "up" a migration instance.
     *
     * @param  string  $file
     * @param  int     $batch
     * @param  bool    $pretend
     *
     * @return void
     */
    protected function runUp($file, $batch, $pretend)
    {
        $file = $this->getMigrationName($file);

        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $migration = $this->resolve($file);

        if ($pretend) {
            return $this->pretendToRun($migration, 'up');
        }

        $migration->up($key = $this->entity->getKey(), $this->entity);

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($file, $batch);

        $this->note("<info>Migrated [{$this->entity->getTable()}:{$key}]:</info> {$file}");
    }

    /**
     * Rollback the last migration operation.
     *
     * @param  array|string $paths
     * @param  array  $options
     *
     * @return array
     */
    public function rollback($paths = [], array $options = [])
    {
        $this->notes = [];

        $rolledBack = [];

        // We want to pull in the last batch of migrations that ran on the previous
        // migration operation. We'll then reverse those migrations and run each
        // of them "down" to reverse the last migration "operation" which ran.
        if (($steps = Arr::get($options, 'step', 0)) > 0) {
            $migrations = $this->repository->getMigrations($steps);
        } else {
            $migrations = $this->repository->getLast();
        }

        $count = count($migrations);

        $files = $this->getMigrationFiles($paths);

        if ($count === 0) {
            $this->note('<info>Nothing to rollback.</info>');
        } else {
            // Next we will run through all of the migrations and call the "down" method
            // which will reverse each migration in order. This getLast method on the
            // repository already returns these migration's names in reverse order.
            $this->requireFiles($files);

            foreach ($migrations as $migration) {
                $migration = (object) $migration;

                $rolledBack[] = $files[$migration->migration];

                $this->runDown(
                    $files[$migration->migration],
                    $migration,
                    Arr::get($options, 'pretend', false)
                );
            }
        }

        return $rolledBack;
    }

    /**
     * Rolls all of the currently applied migrations back.
     *
     * @param  array|string $paths
     * @param  bool  $pretend
     *
     * @return array
     */
    public function reset($paths = [], $pretend = false)
    {
        $this->notes = [];

        $rolledBack = [];

        $files = $this->getMigrationFiles($paths);

        // Next, we will reverse the migration list so we can run them back in the
        // correct order for resetting this database. This will allow us to get
        // the database back into its "empty" state ready for the migrations.
        $migrations = array_reverse($this->repository->getRan());

        $count = count($migrations);

        if ($count === 0) {
            $this->note('<info>Nothing to rollback.</info>');
        } else {
            $this->requireFiles($files);

            // Next we will run through all of the migrations and call the "down" method
            // which will reverse each migration in order. This will get the database
            // back to its original "empty" state and will be ready for migrations.
            foreach ($migrations as $migration) {
                $rolledBack[] = $files[$migration];

                $this->runDown($files[$migration], (object) ['migration' => $migration], $pretend);
            }
        }

        return $rolledBack;
    }

    /**
     * Run "down" a migration instance.
     *
     * @param  string  $file
     * @param  object  $migration
     * @param  bool    $pretend
     *
     * @return void
     */
    protected function runDown($file, $migration, $pretend)
    {
        $file = $this->getMigrationName($file);

        // First we will get the file name of the migration so we can resolve out an
        // instance of the migration. Once we get an instance we can either run a
        // pretend execution of the migration or we can run the real migration.
        $instance = $this->resolve($file);

        if ($pretend) {
            return $this->pretendToRun($instance, 'down');
        }

        $instance->down($key = $this->entity->getKey(), $this->entity);

        // Once we have successfully run the migration "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.
        $this->repository->delete($migration);

        $this->note("<info>Rolled back [{$this->entity->getTable()}:{$key}]:</info> {$file}");
    }

    /**
     * Pretend to run the migrations.
     *
     * @param  object  $migration
     * @param  string  $method
     *
     * @return void
     */
    protected function pretendToRun($migration, $method)
    {
        $table = $this->entity->getTable();
        $key   = $this->entity->getKey();

        foreach ($this->getQueries($migration, $method) as $query) {
            $name = get_class($migration);

            $this->note("<info>{$name} [{$table}:{$key}]:</info> {$query['query']}");
        }
    }

    /**
     * Get all of the queries that would be run for a migration.
     *
     * @param  object  $migration
     * @param  string  $method
     *
     * @return array
     */
    protected function getQueries($migration, $method)
    {
        $connection = $migration->getConnection();

        // Now that we have the connections we can resolve it and pretend to run the
        // queries against the database returning the array of raw SQL statements
        // that would get fired against the database system for this migration.
        $db = $this->resolveConnection($connection);

        return $db->pretend(function () use ($migration, $method) {
            call_user_func([$migration, $method], $this->entity->getKey(), $this->entity);
        });
    }
}
