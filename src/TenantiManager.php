<?php namespace Orchestra\Tenanti;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Support\Manager;
use Orchestra\Tenanti\Migrator\Factory;

class TenantiManager extends Manager
{
    /**
     * Configuration values.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Migration factory resolver.
     *
     * @var string
     */
    protected $resolver = Factory::class;

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     *
     * @return \Orchestra\Tenanti\Migrator\FactoryInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        $config = Arr::get($this->config, "drivers.{$driver}");
        $chunk  = Arr::get($this->config, 'chunk', 100);

        if (is_null($config)) {
            throw new InvalidArgumentException("Driver [$driver] not supported.");
        }

        return $this->app->make($this->resolver, [$this->app, $driver, $config, $chunk]);
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('Default driver not implemented.');
    }

    /**
     * Get configuration values.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set configuration.
     *
     * @param  array  $config
     *
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }
}
