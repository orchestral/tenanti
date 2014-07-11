<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Illuminate\Container\Container;
use Mockery as m;
use Orchestra\Tenanti\Migrator\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::runInstall()
     * method.
     *
     * @test
     */
    public function testRunInstallMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = array();

        $schema = m::mock('\Illuminate\Database\Schema\Builder');

        $schema->shouldReceive('hasTable')->once()->with('user_5_migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('user_5_migrations', m::type('Closure'))->andReturnNull();

        $app['db']->shouldReceive('connection')->twice()->with('primary')->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->twice()->andReturn($schema);

        $stub = new Factory($app, $driver, $config);

        $model = $this->getMockModel();

        $this->assertNull($stub->runInstall($model, 'primary'));
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function getMockModel()
    {
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $model->shouldReceive('getKey')->andReturn(5);

        return $model;
    }

    /**
     * @return \Illuminate\Container\Container
     */
    protected function getAppContainer()
    {
        $app = new Container;
        $app['db'] = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');

        return $app;
    }
}
