<?php namespace Orchestra\Tenanti;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
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
        $this->app['orchestra.commands.tenanti.migrate'] = $this->app->share(function () {
            return new Console\MigrateCommand;
        });

        $this->app['orchestra.commands.tenanti.create-migration'] = $this->app->share(function () {
            return new Console\CreateMigrationCommand;
        });

        $this->commands('orchestra.commands.tenanti.migrate', 'orchestra.commands.tenanti.create-migration');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'orchestra.commands.tenanti.migrate',
            'orchestra.commands.tenanti.create-migration',
        ];
    }
}
