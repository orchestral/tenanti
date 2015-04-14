<?php namespace Orchestra\Tenanti;

use Illuminate\Support\ServiceProvider;
use Orchestra\Tenanti\Migrator\Creator;
use Orchestra\Tenanti\Console\ResetCommand;
use Orchestra\Tenanti\Console\QueuedCommand;
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
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Queued'   => 'orchestra.commands.tenanti.queue',
        'Install'  => 'orchestra.commands.tenanti.install',
        'Make'     => 'orchestra.tenanti.tenanti.make',
        'Migrate'  => 'orchestra.commands.tenanti.migrate',
        'Rollback' => 'orchestra.commands.tenanti.rollback',
        'Reset'    => 'orchestra.commands.tenanti.reset',
        'Refresh'  => 'orchestra.commands.tenanti.refresh',
    ];

     /**
     * Additional provides.
     *
     * @var array
     */
    protected $provides = [
        'orchestra.tenanti.creator',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($this->commands));
    }

    /**
     * Register the "queue" migration command.
     *
     * @return void
     */
    protected function registerQueuedCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.queue', function ($app) {
            return new QueuedCommand($app['orchestra.tenanti']);
        });
    }

    /**
     * Register the "install" migration command.
     *
     * @return void
     */
    protected function registerInstallCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.install', function ($app) {
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
        $this->app->singleton('orchestra.tenanti.creator', function ($app) {
            return new Creator($app['files']);
        });

        $this->app->singleton('orchestra.commands.tenanti.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            return new MigrateMakeCommand(
                $app['orchestra.tenanti'],
                $app['orchestra.tenanti.creator'],
                $app['composer']
            );
        });
    }

    /**
     * Register the "migrate" migration command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.migrate', function ($app) {
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
        $this->app->singleton('orchestra.commands.tenanti.rollback', function ($app) {
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
        $this->app->singleton('orchestra.commands.tenanti.reset', function ($app) {
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
        $this->app->singleton('orchestra.commands.tenanti.refresh', function ($app) {
            return new RefreshCommand($app['orchestra.tenanti']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->commands), $this->provides);
    }
}
