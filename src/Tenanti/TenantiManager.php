<?php namespace Orchestra\Tenanti;

use InvalidArgumentException;
use Illuminate\Support\Manager;

class TenantiManager extends Manager
{
    /**
     * Migration factory resolver.
     *
     * @var string
     */
    protected $resolver = 'Orchestra\Tenanti\MigratorFactoryInterface';

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        $config = $this->app['config']->get("orchestra/tenanti::drivers.{$driver}");

        if (is_null($config)) {
            throw new InvalidArgumentException("Driver [$driver] not supported.");
        }

        return $this->app->make($this->resolver, array($this->app, $driver, $config));
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException("Default driver not implemented.");
    }
}
