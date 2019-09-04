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
    protected $configurations = [];

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
        if (\is_null($this->setupDriverConfig($driver))) {
            throw new InvalidArgumentException("Driver [$driver] not supported.");
        }

        return new $this->resolver($this->container, $this, $driver);
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
     * Get Tenanti configuration.
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configurations;
    }

    /**
     * Set configuration.
     *
     * @param  array  $config
     *
     * @return $this
     */
    public function setConfiguration(array $config)
    {
        $this->configurations = \array_merge($config, ['connection' => $this->config('connection')]);

        return $this;
    }

    /**
     * Get configuration values.
     *
     * @param  string|null  $group
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function config(?string $group = null, $default = null)
    {
        return Arr::get($this->configurations, $group, $default);
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
        if (\is_null($using)) {
            $using = $this->config->get('database.default');
        }

        $config = $this->config->get("database.connections.{$using}", null);

        if (\is_null($config)) {
            throw new InvalidArgumentException("Database connection [{$using}] is not available.");
        }

        Arr::set($this->configurations, 'connection', [
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
        if (isset($this->configurations[$driver])) {
            return null;
        }

        if (\is_null($config = Arr::pull($this->configurations, "drivers.{$driver}"))) {
            return null;
        }

        $connection = $this->configurations['connection'] ?? null;

        if (! \is_null($connection) && $this->driverExcludedByOptions($driver, $connection['options'])) {
            $connection = null;
        }

        return $this->configurations[$driver] = \array_merge($config, ['connection' => $connection]);
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
        return (! empty($options['only']) && ! \in_array($driver, (array) $options['only'])) ||
            (! empty($options['except']) && \in_array($driver, (array) $options['except']));
    }
}
