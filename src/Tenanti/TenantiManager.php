<?php namespace Orchestra\Tenanti;

use Illuminate\Support\Manager;

class TenantiManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new \InvalidArgumentException("Default driver not implemented.");
    }
}
