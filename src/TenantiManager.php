<?php namespace Orchestra\Tenanti;

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
        $config = Arr::pull($this->config, "drivers.{$driver}");
        $chunk  = Arr::pull($this->config, 'chunk', 100);

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

    /**
     * Setup multiple database connection from template.
     *
     * @param  string  $connection
     * @param  \Closure  $callback
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setupMultiDatabase($connection, Closure $callback)
    {
        $repository = $this->app->make('config');

        if (is_null($connection)) {
            $connection = $repository->get('database.default');
        }

        $config = $repository->get("database.connections.{$connection}", null);

        if (is_null($config)) {
            throw new InvalidArgumentException("Database connection [{$connection}] is not available.");
        }

        Arr::set($this->config, 'database', [
            'template' => $config,
            'resolver' => $callback,
        ]);
    }
}
