<?php namespace Orchestra\Tenanti;

use Illuminate\Database\Eloquent\Model;

class Migrator extends \Illuminate\Database\Migrations\Migrator
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
     * @param  \Illuminate\Database\Eloquent\Model $entity
     * @return Migrator
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

        $migration->up($this->entity->getKey(), $this->entity);

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($file, $batch);

        $this->note("<info>Migrated:</info> $file");
    }

    /**
     * Run "down" a migration instance.
     *
     * @param  object  $migration
     * @param  bool    $pretend
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

        $instance->down($this->entity->getKey(), $this->entity);

        // Once we have successfully run the migration "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.
        $this->repository->delete($migration);

        $this->note("<info>Rolled back:</info> $file");
    }
}
