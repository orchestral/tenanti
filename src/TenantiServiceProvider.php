<?php namespace Orchestra\Tenanti;

use Illuminate\Contracts\Foundation\Application;
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
        $this->app->singleton('orchestra.tenanti', function (Application $app) {
            $manager = new TenantiManager($app);
            $namespace = $this->hasPackageRepository() ? 'orchestra/tenanti::' : 'orchestra.tenanti';

            $manager->setConfig($app->make('config')->get($namespace));

            return $manager;
        });

        $this->app->alias('orchestra.tenanti', TenantiManager::class);
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../resources');

        $this->addConfigComponent('orchestra/tenanti', 'orchestra/tenanti', "{$path}/config");

        if (! $this->hasPackageRepository()) {
            $this->bootUsingLaravel($path);
        }
    }

    /**
     * Boot using Laravel setup.
     *
     * @param  string  $path
     *
     * @return void
     */
    protected function bootUsingLaravel($path)
    {
        $this->mergeConfigFrom("{$path}/config/config.php", 'orchestra.tenanti');

        $this->publishes([
            "{$path}/config/config.php" => config_path('orchestra/tenanti.php'),
        ]);
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
