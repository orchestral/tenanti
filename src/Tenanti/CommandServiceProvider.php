<?php namespace Orchestra\Tenanti;

use Illuminate\Support\ServiceProvider;
use Orchestra\Tenanti\Console\ResetCommand;
use Orchestra\Tenanti\Console\RefreshCommand;
use Orchestra\Tenanti\Console\InstallCommand;
use Orchestra\Tenanti\Console\MigrateCommand;
use Orchestra\Tenanti\Console\RollbackCommand;
use Orchestra\Tenanti\Console\MigrateMakeCommand;

class CommandServiceProvider extends ServiceProvider
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
        $commands = array('Migrate', 'Install', 'Rollback', 'Reset', 'Refresh', 'Make');

        // We'll simply spin through the list of commands that are migration related
        // and register each one of them with an application container. They will
        // be resolved in the Artisan start file and registered on the console.
        foreach ($commands as $command) {
            $this->{'register'.$command.'Command'}();
        }

        // Once the commands are registered in the application IoC container we will
        // register them with the Artisan start event so that these are available
        // when the Artisan application actually starts up and is getting used.
        $this->commands(
            'orchestra.commands.tenanti',
            'orchestra.commands.tenanti.make',
            'orchestra.commands.tenanti.install',
            'orchestra.commands.tenanti.rollback',
            'orchestra.commands.tenanti.reset',
            'orchestra.commands.tenanti.refresh'
        );
    }

    /**
     * Register the "migrate" migration command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->bindShared('orchestra.commands.tenanti', function ($app) {
            return new MigrateCommand($app['orchestra.tenanti']);
        });
    }

    /**
     * Register the "rollback" migration command.
     *
     * @return void
     */
    protected function registerRollbackCommand()
    {
        $this->app->bindShared('orchestra.commands.tenanti.rollback', function ($app) {
            return new RollbackCommand($app['orchestra.tenanti']);
        });
    }

    /**
     * Register the "reset" migration command.
     *
     * @return void
     */
    protected function registerResetCommand()
    {
        $this->app->bindShared('orchestra.commands.tenanti.reset', function ($app) {
            return new ResetCommand($app['orchestra.tenanti']);
        });
    }

    /**
     * Register the "refresh" migration command.
     *
     * @return void
     */
    protected function registerRefreshCommand()
    {
        $this->app->bindShared('orchestra.commands.tenanti.refresh', function ($app) {
            return new RefreshCommand($app['orchestra.tenanti']);
        });
    }

    /**
     * Register the "install" migration command.
     *
     * @return void
     */
    protected function registerInstallCommand()
    {
        $this->app->bindShared('orchestra.commands.tenanti.install', function ($app) {
            return new InstallCommand($app['orchestra.tenanti']);
        });
    }

    /**
     * Register the "install" migration command.
     *
     * @return void
     */
    protected function registerMakeCommand()
    {
        $this->app->bindShared('orchestra.commands.tenanti.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            return new MigrateMakeCommand($app['orchestra.tenanti'], $creator);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'orchestra.commands.tenanti',
            'orchestra.commands.tenanti.rollback',
            'orchestra.commands.tenanti.reset',
            'orchestra.commands.tenanti.refresh',
            'orchestra.commands.tenanti.install',
            'orchestra.commands.tenanti.make',
        );
    }
}
