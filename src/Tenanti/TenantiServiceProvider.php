<?php namespace Orchestra\Tenanti;

use Illuminate\Support\ServiceProvider;

class TenantiServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('orchestra.tenanti', function ($app) {
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
        $path = realpath(__DIR__.'/../');

        $this->package('orchestra/tenanti', 'orchestra/tenanti', $path);
    }
}
