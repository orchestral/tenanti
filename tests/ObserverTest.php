<?php namespace Orchestra\Tenanti\TestCase;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Queue;
use Mockery as m;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $app = new Container();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Observer::created() method.
     *
     * @test
     */
    public function testCreatedMethod()
    {
        $queue = m::mock('\Illuminate\Queue\QueueInterface');
        $stub  = m::mock('\Orchestra\Tenanti\Observer[getDriverName]');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub->shouldReceive('getDriverName')->once()->andReturn('user');
        $model->shouldReceive('getKey')->once()->andReturn(5);
        $queue->shouldReceive('push')->once()
            ->with('Orchestra\Tenanti\Migrator\Queue@create', ['database' => null, 'driver' => 'user', 'id' => 5])
            ->andReturnNull();

        Queue::swap($queue);

        $this->assertTrue($stub->created($model));
    }

    /**
     * Test Orchestra\Tenanti\Observer::deleted() method.
     *
     * @test
     */
    public function testDeletedMethod()
    {
        $queue = m::mock('\Illuminate\Queue\QueueInterface');
        $stub  = m::mock('\Orchestra\Tenanti\Observer[getConnectionName,getDriverName]');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub->shouldReceive('getDriverName')->once()->andReturn('user')
            ->shouldReceive('getConnectionName')->once()->andReturn('primary');
        $model->shouldReceive('getKey')->once()->andReturn(5);
        $queue->shouldReceive('push')->once()
            ->with('Orchestra\Tenanti\Migrator\Queue@delete', ['database' => 'primary', 'driver' => 'user', 'id' => 5])
            ->andReturnNull();

        Queue::swap($queue);

        $this->assertTrue($stub->deleted($model));
    }
}
