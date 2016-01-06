<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Orchestra\Tenanti\Migrator\Queue;

class QueueTest extends \PHPUnit_Framework_TestCase
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
     * Test Orchestra\Tenanti\Migrator\Queue::create() method.
     *
     * @test
     */
    public function testCreateMethod()
    {
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Factory');
        $tenanti = $this->app['orchestra.tenanti'];
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub = new Queue();
        $job = m::mock('\Illuminate\Contracts\Queue\Job');
        $data = [
            'database' => 'foo',
            'driver'   => 'user',
            'id'       => 5,
        ];

        $tenanti->shouldReceive('driver')->once()->andReturn($migrator);
        $migrator->shouldReceive('runInstall')->once()->with($model, 'foo')->andReturnNull()
            ->shouldReceive('runUp')->once()->with($model, 'foo')->andReturnNull()
            ->shouldReceive('getModel->find')->with(5)->andReturn($model);
        $job->shouldReceive('delete')->once()->andReturnNull();

        App::swap($this->app);

        $this->assertNull($stub->create($job, $data));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Queue::create() method
     * when model is null.
     *
     * @test
     */
    public function testCreateMethodWhenModelIsNull()
    {
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Factory');
        $tenanti = $this->app['orchestra.tenanti'];

        $stub = new Queue();
        $job = m::mock('\Illuminate\Contracts\Queue\Job');
        $data = [
            'database' => 'foo',
            'driver'   => 'user',
            'id'       => 5,
        ];

        $tenanti->shouldReceive('driver')->once()->andReturn($migrator);
        $migrator->shouldReceive('getModel->find')->with(5)->andReturnNUll();
        $job->shouldReceive('delete')->once()->andReturnNull();

        App::swap($this->app);

        $this->assertNull($stub->create($job, $data));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Queue::delete() method.
     *
     * @test
     */
    public function testDeleteMethod()
    {
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Factory');
        $tenanti = $this->app['orchestra.tenanti'];
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub = new Queue();
        $job = m::mock('\Illuminate\Contracts\Queue\Job');
        $data = [
            'database' => 'foo',
            'driver'   => 'user',
            'id'       => 5,
        ];

        $tenanti->shouldReceive('driver')->once()->andReturn($migrator);
        $migrator->shouldReceive('runReset')->once()->with($model, 'foo')->andReturnNull()
            ->shouldReceive('getModel->find')->with(5)->andReturn($model);
        $job->shouldReceive('delete')->once()->andReturnNull();

        App::swap($this->app);

        $this->assertNull($stub->delete($job, $data));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Queue::delete() method
     * when model is null.
     *
     * @test
     */
    public function testDeleteMethodWhenModelIsNull()
    {
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Factory');
        $tenanti = $this->app['orchestra.tenanti'];

        $stub = new Queue();
        $job = m::mock('\Illuminate\Contracts\Queue\Job');
        $data = [
            'database' => 'foo',
            'driver'   => 'user',
            'id'       => 5,
        ];

        $tenanti->shouldReceive('driver')->once()->andReturn($migrator);
        $migrator->shouldReceive('getModel->find')->with(5)->andReturnNUll();
        $job->shouldReceive('delete')->once()->andReturnNull();

        App::swap($this->app);

        $this->assertNull($stub->delete($job, $data));
    }
}
