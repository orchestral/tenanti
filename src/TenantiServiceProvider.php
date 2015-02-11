<?php namespace Orchestra\Tenanti;

use Orchestra\Support\Providers\ServiceProvider;

class TenantiServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.tenanti', function ($app) {
            return new TenantiManager($app);
        });

        $this->app->alias('orchestra.tenanti', 'Orchestra\Tenanti\TenantiManager');
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../resources');

        $this->addConfigComponent('orchestra/tenanti', 'orchestra/tenanti', $path.'/config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['orchestra.tenanti'];
    }
}
