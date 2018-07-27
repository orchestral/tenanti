<?php

namespace Orchestra\Tenanti\Eloquent;

use Illuminate\Database\Eloquent\Builder as Eloquent;

class Builder extends Eloquent
{
    /**
     * Create a new instance of the model being queried.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModelInstance($attributes = [])
    {
        return parent::newModelInstance($attributes)->setTenantor($this->model);
    }
}
