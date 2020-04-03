<?php

namespace Orchestra\Tenanti;

use Orchestra\Support\Providers\CommandServiceProvider as ServiceProvider;
use Orchestra\Tenanti\Console\InstallCommand;
use Orchestra\Tenanti\Console\MigrateCommand;
use Orchestra\Tenanti\Console\MigrateMakeCommand;
use Orchestra\Tenanti\Console\QueuedCommand;
use Orchestra\Tenanti\Console\RefreshCommand;
use Orchestra\Tenanti\Console\ResetCommand;
use Orchestra\Tenanti\Console\RollbackCommand;
use Orchestra\Tenanti\Console\TinkerCommand;
use Orchestra\Tenanti\Migrator\Creator;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Queued' => 'orchestra.commands.tenanti.queue',
        'Install' => 'orchestra.commands.tenanti.install',
        'Make' => 'orchestra.commands.tenanti.make',
        'Migrate' => 'orchestra.commands.tenanti.migrate',
        'Rollback' => 'orchestra.commands.tenanti.rollback',
        'Reset' => 'orchestra.commands.tenanti.reset',
        'Refresh' => 'orchestra.commands.tenanti.refresh',
        'Tinker' => 'orchestra.commands.tenanti.tinker',
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
     */
    protected function registerQueuedCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.queue', static function () {
            return new QueuedCommand();
        });
    }

    /**
     * Register the "install" migration command.
     */
    protected function registerInstallCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.install', static function () {
            return new InstallCommand();
        });
    }

    /**
     * Register the "make" migration command.
     */
    protected function registerMakeCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.make', static function () {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            return new MigrateMakeCommand();
        });
    }

    /**
     * Register the "migrate" migration command.
     */
    protected function registerMigrateCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.migrate', static function () {
            return new MigrateCommand();
        });
    }

    /**
     * Register the "rollback" migration command.
     */
    protected function registerRollbackCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.rollback', static function () {
            return new RollbackCommand();
        });
    }

    /**
     * Register the "reset" migration command.
     */
    protected function registerResetCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.reset', static function () {
            return new ResetCommand();
        });
    }

    /**
     * Register the "refresh" migration command.
     */
    protected function registerRefreshCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.refresh', static function () {
            return new RefreshCommand();
        });
    }

    /**
     * Register the "tinker" migration command.
     */
    protected function registerTinkerCommand(): void
    {
        $this->app->singleton('orchestra.commands.tenanti.tinker', static function () {
            return new TinkerCommand();
        });
    }
}
