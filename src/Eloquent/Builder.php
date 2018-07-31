<?php

namespace Orchestra\Tenanti\Eloquent;

use Orchestra\Tenanti\Tenantor;
use Illuminate\Database\Eloquent\Builder as Eloquent;

class Builder extends Eloquent
{
    /**
     * The tenantor associated with the model.
     *
     * @var \Orchestra\Tenanti\Tenantor
     */
    protected $tenantor;

    /**
     * Create a new instance of the model being queried.
     *
     * @param  array  $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModelInstance($attributes = [])
    {
        return tap(parent::newModelInstance($attributes), function ($model) {
            $model->setTenantor($this->tenantor);
        });
    }

    /**
     * Get the tenantor associated with the model.
     *
     * @return \Orchestra\Tenanti\Tenantor|null
     */
    public function getTenantor(): ?Tenantor
    {
        return $this->tenantor;
    }

    /**
     * Get the tenantor associated with the model.
     *
     * @param  \Orchestra\Tenanti\Tenantor|null  $tenantor
     *
     * @return $this
     */
    public function setTenantor(?Tenantor $tenantor)
    {
        $this->tenantor = $tenantor;

        return $this;
    }
}
