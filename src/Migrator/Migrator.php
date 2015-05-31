<?php namespace Orchestra\Tenanti\Migrator;

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
     * Run "down" a migration instance.
     *
     * @param  object  $migration
     * @param  bool    $pretend
     *
     * @return void
     */
    protected function runDown($migration, $pretend)
    {
        $file = $migration->migration;

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
