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

        $this->config[$driver] = array_merge($config, ['connection' => Arr::get($this->config, 'connection')]);

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
     * @param  string  $using
     * @param  \Closure  $callback
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function connection($using, Closure $callback)
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
        ]);
    }

    /**
     * Setup multiple database connection from template.
     *
     * @param  string  $using
     * @param  \Closure  $callback
     *
     * @return void
     *
     * @deprecated since 3.1.x and to be removed in 3.3.0.
     *
     * @throws \InvalidArgumentException
     */
    public function setupMultiDatabase($using, Closure $callback)
    {
        return $this->connection($using, $callback);
    }
}
