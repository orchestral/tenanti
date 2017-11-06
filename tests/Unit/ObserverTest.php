<?php

namespace Orchestra\Tenanti\Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

class ObserverTest extends TestCase
{
    /**
     * @var \Illuminate\Container\Container
     */
    private $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
        Container::setInstance($this->app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);

        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Observer::created() method.
     *
     * @test
     */
    public function testCreatedMethod()
    {
        $app = $this->app;
        $app['Illuminate\Contracts\Bus\Dispatcher'] = $bus = m::mock('Illuminate\Contracts\Bus\Dispatcher');

        $config = ['database' => null, 'driver' => 'user'];

        $stub = m::mock('\Orchestra\Tenanti\Observer[getDriverName,getCreateTenantJob]')
                    ->shouldAllowMockingProtectedMethods();

        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $job = m::mock('\Orchestra\Tenanti\Jobs\CreateTenant', [$model, $config]);

        $stub->shouldReceive('getDriverName')->once()->andReturn('user')
            ->shouldReceive('getCreateTenantJob')->once()->with($model, $config)->andReturn($job);
        $bus->shouldReceive('dispatch')->once()->with($job)->andReturnNull();

        $this->assertTrue($stub->created($model));
    }

    /**
     * Test Orchestra\Tenanti\Observer::deleted() method.
     *
     * @test
     */
    public function testDeletedMethod()
    {
        $app = $this->app;
        $app['Illuminate\Contracts\Bus\Dispatcher'] = $bus = m::mock('Illuminate\Contracts\Bus\Dispatcher');

        $config = ['database' => 'primary', 'driver' => 'user'];

        $stub = m::mock('\Orchestra\Tenanti\Observer[getConnectionName,getDriverName,getDeleteTenantJob]')
                    ->shouldAllowMockingProtectedMethods();

        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $job = m::mock('\Orchestra\Tenanti\Jobs\DeleteTenant', [$model, $config]);

        $stub->shouldReceive('getDriverName')->once()->andReturn('user')
            ->shouldReceive('getConnectionName')->once()->andReturn('primary')
            ->shouldReceive('getDeleteTenantJob')->once()->with($model, $config)->andReturn($job);
        $bus->shouldReceive('dispatch')->once()->with($job)->andReturnNull();

        $this->assertTrue($stub->deleted($model));
    }
}
