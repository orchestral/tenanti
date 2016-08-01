<?php

namespace Orchestra\Tenanti;

use Closure;
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
        $chunk = Arr::get($this->config, 'chunk', 100);

        if (is_null($this->setupDriverConfig($driver))) {
            throw new InvalidArgumentException("Driver [$driver] not supported.");
        }

        return $this->app->make($this->resolver, [$this->app, $this, $driver, $chunk]);
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
     * @param  string|null  $group
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getConfig($group = null, $default = null)
    {
        return Arr::get($this->config, $group, $default);
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
        $this->config = array_merge($config, ['connection' => $this->getConfig('connection')]);

        return $this;
    }

    /**
     * Setup multiple database connection from template.
     *
     * @param  string  $using
     * @param  \Closure  $callback
     * @param  array  $option
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function connection($using, Closure $callback, array $options = [])
    {
        $repository = $this->app->make('config');

        if (is_null($using)) {
            $using = $repository->get('database.default');
        }

        $config = $repository->get("database.connections.{$using}", null);

        if (is_null($config)) {
            throw new InvalidArgumentException("Database connection [{$using}] is not available.");
        }

        Arr::set($this->config, 'connection', [
            'name'     => "{$using}_{id}",
            'template' => $config,
            'resolver' => $callback,
            'options'  => $options,
        ]);
    }

    /**
     * Prepare configuration values.
     *
     * @param  string  $driver
     *
     * @return array|null
     */
    protected function setupDriverConfig($driver)
    {
        if (isset($this->config[$driver])) {
            return;
        }

        if (is_null($config = Arr::pull($this->config, "drivers.{$driver}"))) {
            return;
        }

        $connection = Arr::get($this->config, 'connection');

        if (! is_null($connection) && $this->driverExcludedByOptions($driver, $connection['options'])) {
            $connection = null;
        }

        return $this->config[$driver] = array_merge($config, ['connection' => $connection]);
    }

    /**
     * Determine if the given options exclude a particular driver.
     *
     * @param  string  $driver
     * @param  array  $options
     *
     * @return bool
     */
    protected function driverExcludedByOptions($driver, array $options)
    {
        return (! empty($options['only']) && ! in_array($driver, (array) $options['only'])) ||
            (! empty($options['except']) && in_array($driver, (array) $options['except']));
    }
}
