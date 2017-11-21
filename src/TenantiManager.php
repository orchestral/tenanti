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
     * @throws \InvalidArgumentException
     *
     * @return \Orchestra\Tenanti\Contracts\Factory
     */
    protected function createDriver($driver): Contracts\Factory
    {
        if (is_null($this->setupDriverConfig($driver))) {
            throw new InvalidArgumentException("Driver [$driver] not supported.");
        }

        return new $this->resolver($this->app, $this, $driver);
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
    public function getConfig(?string $group = null, $default = null)
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
    public function setConfig(array $config): self
    {
        $this->config = array_merge($config, ['connection' => $this->getConfig('connection')]);

        return $this;
    }

    /**
     * Setup multiple database connection from template.
     *
     * @param  string|null  $using
     * @param  \Closure  $callback
     * @param  array  $option
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function connection(?string $using, Closure $callback, array $options = []): void
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
            'name' => "{$using}_{id}",
            'template' => $config,
            'resolver' => $callback,
            'options' => $options,
        ]);
    }

    /**
     * Prepare configuration values.
     *
     * @param  string  $driver
     *
     * @return array|null
     */
    protected function setupDriverConfig(string $driver): ?array
    {
        if (isset($this->config[$driver])) {
            return null;
        }

        if (is_null($config = Arr::pull($this->config, "drivers.{$driver}"))) {
            return null;
        }

        $connection = $this->config['connection'] ?? null;

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
    protected function driverExcludedByOptions(string $driver, array $options): bool
    {
        return (! empty($options['only']) && ! in_array($driver, (array) $options['only'])) ||
            (! empty($options['except']) && in_array($driver, (array) $options['except']));
    }
}
