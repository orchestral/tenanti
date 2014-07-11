<?php namespace Orchestra\Tenanti;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Queue;

abstract class Observer
{
    /**
     * Get driver name.
     */
    abstract public function getDriverName();

    /**
     * Run on created observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model $entity
     * @return bool
     */
    public function created(Model $entity)
    {
        Queue::push('Orchestra\Tenanti\Queue@create', array(
            'driver' => $this->getDriverName(),
            'id'     => $entity->getKey(),
        ));

        return true;
    }

    /**
     * Run on deleted observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model $entity
     * @return bool
     */
    public function deleted(Model $entity)
    {
        Queue::push('Orchestra\Tenanti\Queue@delete', array(
            'driver' => $this->getDriverName(),
            'id'     => $entity->getKey(),
        ));

        return true;
    }
}
