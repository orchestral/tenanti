<?php namespace Orchestra\Tenanti\Jobs\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Orchestra\Tenanti\Jobs\DeleteTenant;

class DeleteTenantTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
        $this->app['orchestra.tenanti'] = m::mock('\Orchestra\Tenanti\TenantiManager');

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
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
     * Test Orchestra\Tenanti\Jobs\DeleteTenant::fire() method.
     *
     * @test
     */
    public function testFireMethod()
    {
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Factory');
        $tenanti  = $this->app['orchestra.tenanti'];

        $stub = new DeleteTenant();
        $job  = m::mock('\Illuminate\Contracts\Queue\Job');
        $data = [
            'database' => 'foo',
            'driver'   => 'user',
            'id'       => 5,
        ];

        $tenanti->shouldReceive('driver')->once()->andReturn($migrator);
        $migrator->shouldReceive('getModel->newInstance->find')->with(5)->andReturnNUll();
        $job->shouldReceive('delete')->once()->andReturnNull();

        App::swap($this->app);

        $this->assertNull($stub->fire($job, $data));
    }
}
