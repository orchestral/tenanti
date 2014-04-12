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
        $this->app->bindShared('orchestra.commands.tenanti.migrate', function ($app) {
            return new Console\MigrateCommand(new Migrator($app['config']));
        });

        $this->app->bindShared('orchestra.commands.tenanti.setup', function ($app) {
            return new Console\SetupCommand($app['files']);
        });

        $this->commands('orchestra.commands.tenanti.migrate', 'orchestra.commands.tenanti.setup');
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
            'orchestra.commands.tenanti.setup',
        ];
    }
}
