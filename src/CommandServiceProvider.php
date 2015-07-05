<?php namespace Orchestra\Tenanti;

use Orchestra\Tenanti\Migrator\Creator;
use Orchestra\Tenanti\Console\ResetCommand;
use Orchestra\Tenanti\Console\QueuedCommand;
use Orchestra\Tenanti\Console\InstallCommand;
use Orchestra\Tenanti\Console\MigrateCommand;
use Orchestra\Tenanti\Console\RefreshCommand;
use Orchestra\Tenanti\Console\RollbackCommand;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Tenanti\Console\MigrateMakeCommand;
use Orchestra\Support\Providers\CommandServiceProvider as ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Queued'   => 'orchestra.commands.tenanti.queue',
        'Install'  => 'orchestra.commands.tenanti.install',
        'Make'     => 'orchestra.commands.tenanti.make',
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
     * Register the "queue" migration command.
     *
     * @return void
     */
    protected function registerQueuedCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.queue', function (Application $app) {
            return new QueuedCommand($app->make('orchestra.tenanti'));
        });
    }

    /**
     * Register the "install" migration command.
     *
     * @return void
     */
    protected function registerInstallCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.install', function (Application $app) {
            return new InstallCommand($app->make('orchestra.tenanti'));
        });
    }

    /**
     * Register the "install" migration command.
     *
     * @return void
     */
    protected function registerMakeCommand()
    {
        $this->app->singleton('orchestra.tenanti.creator', function (Application $app) {
            return new Creator($app->make('files'));
        });

        $this->app->singleton('orchestra.commands.tenanti.make', function (Application $app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            return new MigrateMakeCommand(
                $app->make('orchestra.tenanti'),
                $app->make('orchestra.tenanti.creator'),
                $app->make('composer')
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
        $this->app->singleton('orchestra.commands.tenanti.migrate', function (Application $app) {
            return new MigrateCommand($app->make('orchestra.tenanti'));
        });
    }

    /**
     * Register the "rollback" migration command.
     *
     * @return void
     */
    protected function registerRollbackCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.rollback', function (Application $app) {
            return new RollbackCommand($app->make('orchestra.tenanti'));
        });
    }

    /**
     * Register the "reset" migration command.
     *
     * @return void
     */
    protected function registerResetCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.reset', function (Application $app) {
            return new ResetCommand($app->make('orchestra.tenanti'));
        });
    }

    /**
     * Register the "refresh" migration command.
     *
     * @return void
     */
    protected function registerRefreshCommand()
    {
        $this->app->singleton('orchestra.commands.tenanti.refresh', function (Application $app) {
            return new RefreshCommand($app->make('orchestra.tenanti'));
        });
    }
}
