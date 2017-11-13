<?php

namespace Orchestra\Tenanti\Tests\Unit\Jobs;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Orchestra\Tenanti\Jobs\CreateTenant;

class CreateTenantTest extends TestCase
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
        $this->app['orchestra.tenanti'] = m::mock('\Orchestra\Tenanti\TenantiManager');

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
     * Test Orchestra\Tenanti\Jobs\CreateTenant::fire() method.
     *
     * @test
     */
    public function testHandleMethod()
    {
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Factory');
        $tenanti = $this->app['orchestra.tenanti'];
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $data = [
            'database' => 'foo',
            'driver' => 'user',
        ];

        $stub = new CreateTenant($model, $data);

        $model->shouldReceive('getKey')->never()->andReturn(4);

        $tenanti->shouldReceive('driver')->once()->andReturn($migrator);
        $migrator->shouldReceive('runInstall')->once()->with($model, 'foo')->andReturnNull()
            ->shouldReceive('runUp')->once()->with($model, 'foo')->andReturnNull();

        App::swap($this->app);

        $this->assertNull($stub->handle());
    }
}
