<?php

namespace Orchestra\Tenanti\Eloquent;

use Illuminate\Database\Eloquent\Builder as Eloquent;
use Orchestra\Tenanti\Tenantor;

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
        return \tap(parent::newModelInstance($attributes), function ($model) {
            $model->setTenantor($this->tenantor);
        });
    }

    /**
     * Get the tenantor associated with the model.
     */
    public function getTenantor(): ?Tenantor
    {
        return $this->tenantor;
    }

    /**
     * Get the tenantor associated with the model.
     *
     * @return $this
     */
    public function setTenantor(?Tenantor $tenantor)
    {
        $this->tenantor = $tenantor;

        return $this;
    }
}
