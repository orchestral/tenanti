<?php

namespace Orchestra\Tenanti\Tests\Unit\Console;

use Illuminate\Support\Composer;
use Mockery as m;
use Orchestra\Tenanti\Console\MigrateMakeCommand;
use Symfony\Component\Console\Exception\RuntimeException;

class MigrateMakeCommandTest extends CommandTest
{
    public function testMakeWithoutAnyDrivers()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $creator = $this->app['orchestra.tenanti.creator'];
        $composer = m::mock(Composer::class);

        $tenanti->shouldReceive('config')
            ->andReturn([]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver, name"');

        $this->artisan('tenanti:make');
    }

    public function testMakeWithOneDriverWithOneArgument()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $writer = $this->app['Orchestra\Tenanti\Migrator\MigrationWriter'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('config')
            ->andReturn([
                'tenant' => [],
            ]);

        $factory = $this->getMockDriverFactory();

        $tenanti->shouldReceive('driver')
            ->with('tenant')
            ->andReturn($factory);

        $writer->shouldReceive('__invoke')->with('tenant', 'add_migration', null, null)->once()
            ->andReturn('2014_10_12_000000_create_users_table.php');

        $this->app['artisan']->add(new MigrateMakeCommand());
        $this->artisan('tenanti:make', ['driver' => 'tenant', 'name' => 'add_migration']);
    }

    public function testTinkerWithOneDriverWithTwoArguments()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $writer = $this->app['Orchestra\Tenanti\Migrator\MigrationWriter'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('config')
            ->andReturn([
                'tenant1' => [],
            ]);

        $writer->shouldReceive('__invoke')->with('tenant1', 'add_migration', null, null)->once()
            ->andReturn('2014_10_12_000000_create_users_table.php');

        $this->app['artisan']->add(new MigrateMakeCommand());
        $this->artisan('tenanti:make', ['driver' => 'tenant1', 'name' => 'add_migration']);
    }

    public function testTinkerWithTwoDriversWithOneArgument()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $writer = $this->app['Orchestra\Tenanti\Migrator\MigrationWriter'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('config')
            ->andReturn([
                'tenant1' => [
                ],
                'tenant2' => [
                ],
            ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver"');

        $writer->shouldNotReceive('__invoke');

        $this->app['artisan']->add(new MigrateMakeCommand());
        $this->artisan('tenanti:make', ['driver' => 'add_migration']);
    }

    public function testTinkerWithTwoDriversWithTwoArguments()
    {
        $tenanti = $this->app['orchestra.tenanti'];
        $writer = $this->app['Orchestra\Tenanti\Migrator\MigrationWriter'];

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('dumpAutoloads');

        $tenanti->shouldReceive('config')
            ->andReturn([
                'tenant1' => [
                ],
                'tenant2' => [
                ],
            ]);


        $writer->shouldReceive('__invoke')->with('tenant2', 'add_migration', null, null)->once()
            ->andReturn('2014_10_12_000000_create_users_table.php');

        $this->app['artisan']->add(new MigrateMakeCommand());
        $this->artisan('tenanti:make', ['driver' => 'tenant2', 'name' => 'add_migration']);
    }
}
