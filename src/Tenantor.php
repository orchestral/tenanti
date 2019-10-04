<?php

namespace Orchestra\Tenanti;

use Illuminate\Database\Eloquent\Model;
use Orchestra\Support\Fluent;

class Tenantor extends Fluent
{
    /**
     * Make a tenantor instance.
     *
     * @param  string  $name
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|null  $connection
     *
     * @return static
     */
    public static function fromEloquent(string $name, Model $model, ?string $connection = null)
    {
        return static::make(
            $name, $model->getKey(), $connection ?? $model->getConnectionName(), $model
        );
    }

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
    public static function make(string $name, $key, ?string $connection = null, ?Model $model = null)
    {
        return new static(\compact('name', 'key', 'connection', 'model'));
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
