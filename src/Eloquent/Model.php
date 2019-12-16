<?php

namespace Orchestra\Tenanti\Eloquent;

use Orchestra\Model\Eloquent;

abstract class Model extends Eloquent
{
    use Tenantee;

    /**
     * The tenant name.
     *
     * @var string
     */
    protected $tenant;

    /**
     * Create model instance from existing.
     *
     * @return $this
     */
    public function fillFromExistingOrNew(array $attributes = [])
    {
        $this->fill($attributes);

        $this->exists = ! empty($attributes[$this->getKeyName()]);

        return $this;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     *
     * @return \Orchestra\Tenanti\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return \tap(new Builder($query), function ($builder) {
            $builder->setTenantor($this->tenantor ?? null);
        });
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @param bool  $exists
     *
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        return \tap(parent::newInstance($attributes, $exists), function ($model) {
            $model->setTenantor($this->tenantor ?? null);
        });
    }
}
