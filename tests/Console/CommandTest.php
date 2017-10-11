<?php

namespace Orchestra\Tenanti\TestCase\Console;

use Mockery as m;
use Orchestra\Testbench\TestCase;
use Orchestra\Tenanti\TenantiManager;
use Orchestra\Tenanti\TestCase\Kernel;
use Orchestra\Tenanti\Contracts\Notice;
use Orchestra\Tenanti\Migrator\Creator;
use Orchestra\Tenanti\Contracts\Factory;
use Orchestra\Tenanti\CommandServiceProvider;

abstract class CommandTest extends TestCase
{
    protected function getMockDriverFactory()
    {
        $factory = m::mock(Factory::class);
        $notice = m::mock(Notice::class);

        $factory->shouldReceive('install');

        $factory->shouldReceive('setNotice');

        $factory->shouldReceive('run');

        return $factory;
    }

    protected function getPackageProviders($app)
    {
        return [
            CommandServiceProvider::class,
        ];
    }

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('artisan', function ($app) {
            return new \Illuminate\Console\Application($app, $app['events'], $app->version());
        });

        $app->singleton('Illuminate\Contracts\Console\Kernel', Kernel::class);

        $app['orchestra.tenanti.creator'] = m::mock(Creator::class);
        $app['orchestra.tenanti'] = m::mock(TenantiManager::class);
    }

    public function artisan($command, $parameters = [])
    {
        parent::artisan($command, array_merge($parameters, ['--no-interaction' => true]));
    }
}
