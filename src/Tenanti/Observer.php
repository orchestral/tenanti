<?php namespace Orchestra\Tenanti;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Queue;

class Observer
{
    /**
     * Driver name.
     *
     * @var string
     */
    protected $driver;

    /**
     * Construct a new observer object.
     *
     * @param string    $driver
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Run on created observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model $entity
     * @return bool
     */
    public function created(Model $entity)
    {
        Queue::push('Orchestra\Tenanti\Queue@create', array(
            'driver' => $this->driver,
            'id' => $entity->getKey(),
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
            'driver' => $this->driver,
            'id' => $entity->getKey(),
        ));

        return true;
    }
}
