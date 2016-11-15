<?php

namespace Orchestra\Tenanti\TestCase\Console;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery as m;
use Symfony\Component\Console\Exception\RuntimeException;

class TinkerCommandTest extends CommandTest
{

    public function testTinkerWithoutAnyDrivers()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([]);

        $command = m::mock('\Orchestra\Tenanti\Console\TinkerCommand[call]', [$tenanti]);
        $command->shouldReceive('call');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver, id"');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:tinker');
    }

    public function testTinkerWithOneDriverWithOneArgument()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant' => [
                ],
            ]);

        $model = m::mock(Model::class);

        $model->shouldReceive('findOrFail')
            ->with('1');

        $factory = $this->getMockDriverFactory();

        $factory->shouldReceive('getModel')
            ->andReturn($model);

        $factory->shouldReceive('asDefaultConnection')
            ->withAnyArgs();

        $tenanti->shouldReceive('driver')
            ->with('tenant')
            ->andReturn($factory);

        $command = m::mock('Orchestra\Tenanti\Console\TinkerCommand[call]', [$tenanti]);
        $command->shouldReceive('call');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:tinker', ['driver' => '1']);
    }

    public function testTinkerWithOneDriverWithWrongModel()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant' => [
                ],
            ]);

        $model = m::mock(Model::class);

        $model->shouldReceive('findOrFail')
            ->with('1')
            ->andThrow(ModelNotFoundException::class);

        $factory = $this->getMockDriverFactory();

        $factory->shouldReceive('getModel')
            ->andReturn($model);

        $tenanti->shouldReceive('driver')
            ->with('tenant')
            ->andReturn($factory);

        $command = m::mock('Orchestra\Tenanti\Console\TinkerCommand[call]', [$tenanti]);
        $this->expectException(ModelNotFoundException::class);

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:tinker', ['driver' => '1']);
    }

    public function testTinkerWithTwoDriversWithOneArgument()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant1' => [
                ],
                'tenant2' => [
                ],
            ]);

        $command = m::mock('Orchestra\Tenanti\Console\TinkerCommand[call]', [$tenanti]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('missing: "driver"');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:tinker', ['driver' => '1']);
    }

    public function testTinkerWithTwoDriversWithTwoArguments()
    {
        $tenanti = $this->app['orchestra.tenanti'];

        $tenanti->shouldReceive('getConfig')
            ->andReturn([
                'tenant1' => [
                ],
                'tenant2' => [
                ],
            ]);

        $model = m::mock(Model::class);

        $model->shouldReceive('findOrFail')
            ->with('1');

        $factory = $this->getMockDriverFactory();

        $factory->shouldReceive('getModel')
            ->andReturn($model);

        $factory->shouldReceive('asDefaultConnection')
            ->withAnyArgs();

        $tenanti->shouldReceive('driver')
            ->with('tenant2')
            ->andReturn($factory);

        $command = m::mock('Orchestra\Tenanti\Console\TinkerCommand[call]', [$tenanti]);
        $command->shouldReceive('call');

        $this->app['artisan']->add($command);
        $this->artisan('tenanti:tinker', ['driver' => 'tenant2', 'id' => 1]);
    }
}
