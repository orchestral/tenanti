<?php

namespace Orchestra\Tenanti;

use Orchestra\Support\Fluent;
use Illuminate\Database\Eloquent\Model;

class Tenantor extends Fluent
{
    /**
     * Make a tenantor instance.
     *
     * @param  string  $name
     * @param  mixed  $key
     * @param  string|null  $connection
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     *
     * @return static
     */
    public static function make(string $name, $key, $connection = null, ?Model $model = null)
    {
        if ($key instanceof Model) {
            $model = $key;
            $key = $model->getKey();
            $connection = $connection ?? $model->getConnectionName();
        }

        return new static(compact('name', 'key', 'connection', 'model'));
    }

    /**
     * Get tenant model.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getTenantModel(): ?Model
    {
        return $this->attributes['model'];
    }

    /**
     * Get tenant name.
     *
     * @return string
     */
    public function getTenantName(): string
    {
        return $this->attributes['name'];
    }

    /**
     * Get tenant key.
     *
     * @return mixed
     */
    public function getTenantKey()
    {
        return $this->attributes['key'];
    }

    /**
     * Get tenant connection name.
     *
     * @return string|null
     */
    public function getTenantConnectionName()
    {
        return $this->attributes['connection'];
    }
}
