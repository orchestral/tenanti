<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Mockery as m;
use Orchestra\Tenanti\Migrator\OperationTrait;

class OperationTraitTest extends \PHPUnit_Framework_TestCase
{
    use OperationTrait;

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\OperationTrait::resolveModel()
     * method.
     *
     * @test
     */
    public function testResolveModelMethod()
    {
        $this->app = m::mock('\Illuminate\Container\Container[make]');
        $this->config = ['model' => 'User'];

        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $this->app->shouldReceive('make')->once()->with('User')->andReturn($model);

        $this->assertEquals($model, $this->getModel());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\OperationTrait::resolveModel()
     * method throw an exception when model is not an instance of
     * Eloquent.
     *
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function testResolveModelMethodThrowsException()
    {
        $this->app = m::mock('\Illuminate\Container\Container[make]');
        $this->config = ['model' => 'User'];

        $this->app->shouldReceive('make')->once()->with('User')->andReturnNull();

        $this->getModel();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\OperationTrait::getModelName()
     * method.
     *
     * @test
     */
    public function testGetModelNameMethod()
    {
        $this->config = ['model' => 'User'];

        $this->assertEquals('User', $this->getModelName());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\OperationTrait::getMigrationPath()
     * method.
     *
     * @test
     */
    public function testGetMigrationPathMethod()
    {
        $path = realpath(__DIR__);
        $this->config = ['path' => $path];

        $this->assertEquals($path, $this->getMigrationPath());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\OperationTrait::getTablePrefix()
     * method.
     *
     * @test
     */
    public function testGetTablePrefixMethod()
    {
        $this->driver = 'user';

        $this->assertEquals('user_{id}', $this->getTablePrefix());
    }
}
