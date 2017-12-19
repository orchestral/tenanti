<?php

namespace Orchestra\Tenanti;

use Orchestra\Support\Fluent;

class Tenantor extends Fluent
{
    /**
     * Make a tenantor instance.
     *
     * @param  string  $name
     * @param  mixed  $key
     * @param  string  $connection
     *
     * @return static
     */
    public static function make($name, $key, $connection)
    {
        return new static(compact('name', 'key', 'connection'));
    }

    /**
     * Get tenant name.
     *
     * @return string
     */
    public function getTenantName()
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
     * @return string
     */
    public function getTenantConnectionName()
    {
        return $this->attributes['connection'];
    }
}
