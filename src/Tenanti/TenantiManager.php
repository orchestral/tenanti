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
    protected $resolver = 'Orchestra\Tenanti\Migrator\Factory';

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return \Orchestra\Tenanti\Migrator\FactoryInterface
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        $config = $this->app['config']->get("orchestra/tenanti::drivers.{$driver}");
        $chunk  = $this->app['config']->get('orchestra/tenanti::chunk', 100);

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
        throw new InvalidArgumentException("Default driver not implemented.");
    }
}
