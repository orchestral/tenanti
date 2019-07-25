<?php

namespace Orchestra\Tenanti;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Support\Providers\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class TenantiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.tenanti', function (Application $app) {
            $manager = new TenantiManager($app);

            $this->registerConfigurationForManager($manager);

            return $manager;
        });

        $this->app->alias('orchestra.tenanti', TenantiManager::class);
    }

    /**
     * Register configuration for manager.
     *
     * @param  \Orchestra\Tenanti\TenantiManager  $manager
     *
     * @return void
     */
    protected function registerConfigurationForManager(TenantiManager $manager): void
    {
        $namespace = $this->hasPackageRepository() ? 'orchestra/tenanti::' : 'orchestra.tenanti';

        $this->app->booted(static function ($app) use ($manager, $namespace) {
            $manager->setConfig($app->make('config')->get($namespace));
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $path = \realpath(__DIR__.'/../');

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
    protected function bootUsingLaravel(string $path): void
    {
        $this->mergeConfigFrom("{$path}/config/config.php", 'orchestra.tenanti');

        $this->publishes([
            "{$path}/config/config.php" => \config_path('orchestra/tenanti.php'),
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
