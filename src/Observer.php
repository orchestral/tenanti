<?php namespace Orchestra\Tenanti;

use Illuminate\Support\Facades\Queue;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Tenanti\Jobs\CreateTenant;
use Orchestra\Tenanti\Jobs\DeleteTenant;

abstract class Observer
{
    /**
     * Get connection name.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        return;
    }

    /**
     * Get driver name.
     *
     * @return string
     */
    abstract public function getDriverName();

    /**
     * Run on created observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return bool
     */
    public function created(Model $entity)
    {
        Queue::push(CreateTenant::class, [
            'database' => $this->getConnectionName(),
            'driver'   => $this->getDriverName(),
            'id'       => $entity->getKey(),
        ]);

        return true;
    }

    /**
     * Run on deleted observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return bool
     */
    public function deleted(Model $entity)
    {
        Queue::push(DeleteTenant::class, [
            'database' => $this->getConnectionName(),
            'driver'   => $this->getDriverName(),
            'id'       => $entity->getKey(),
        ]);

        return true;
    }
}
