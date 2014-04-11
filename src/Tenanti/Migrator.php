<?php namespace Orchestra\Tenanti;

use Illuminate\Config\Repository;

class Migrator
{
    /**
     * Config repository instance.
     */
    protected $config;

    /**
     * Create a new migration instance.
     *
     * @param  \Illuminate\Config\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Run a migration.
     *
     * @param  string $name
     * @return array
     */
    public function run($name)
    {

    }
}
