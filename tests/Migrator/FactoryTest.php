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
     * Test Orchestra\Tenanti\Migrator\Factory::install() method.
     *
     * @test
     */
    public function testInstallMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runInstall]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = [
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        ];

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runInstall')->once()->with($entity, 'foo')->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                $c($entities);
            });

        $this->assertNull($stub->install('foo'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::install() method.
     *
     * @test
     */
    public function testInstallMethodOnSingleId()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runInstall]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runInstall')->once()->with($entity, 'foo')->andReturnNull();
        $model->shouldReceive('newQuery->findOrFail')->once()->with(10)
            ->andReturn($entity);

        $this->assertNull($stub->install('foo', 10));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::run() method.
     *
     * @test
     */
    public function testRunMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runUp]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = [
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        ];

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runUp')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                $c($entities);
            });

        $this->assertNull($stub->run('foo'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::run() method.
     *
     * @test
     */
    public function testRunMethodOnSingleId()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runUp]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runUp')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->findOrFail')->once()->with(10)
            ->andReturn($entity);

        $this->assertNull($stub->run('foo', 10));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::rollback() method.
     *
     * @test
     */
    public function testRollbackMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runDown]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = [
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        ];

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runDown')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                    $c($entities);
                });

        $this->assertNull($stub->rollback('foo'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::rollback() method.
     *
     * @test
     */
    public function testRollbackMethodOnSingleId()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runDown]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runDown')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->findOrFail')->once()->with(10)
            ->andReturn($entity);

        $this->assertNull($stub->rollback('foo', 10));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::reset() method.
     *
     * @test
     */
    public function testResetMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runReset]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = [
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        ];

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runReset')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                    $c($entities);
                });

        $this->assertNull($stub->reset('foo'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::reset() method.
     *
     * @test
     */
    public function testResetMethodOnSingleId()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runReset]', [$app, $driver, $config]);
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runReset')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->findOrFail')->once()->with(10)
            ->andReturn($entity);

        $this->assertNull($stub->reset('foo', 10));
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
        $config = [];

        $app['schema']->shouldReceive('hasTable')->once()->with('user_5_migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('user_5_migrations', m::type('Closure'))->andReturnNull();

        $stub = new Factory($app, $driver, $config);
        $model = $this->getMockModel();

        $this->assertNull($stub->runInstall($model, 'primary'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::runInstall()
     * method when given custom attribute.
     *
     * @test
     */
    public function testRunInstallMethodWithCustomAttribute()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = ['migration' => 'migrations'];

        $app['schema']->shouldReceive('hasTable')->once()->with('migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('migrations', m::type('Closure'))->andReturnNull();

        $app['db']->shouldReceive('connection')->with('tenant_foo')->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->andReturn($app['schema']);

        $stub = new Factory($app, $driver, $config);
        $model = $this->getMockModel();

        $this->assertNull($stub->runInstall($model, 'tenant_{entity.username}'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::runInstall()
     * method when database connection name is not available.
     *
     * @test
     */
    public function testRunInstallMethodWithoutDatabaseConnectionName()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = ['migration' => 'migrations'];

        $app['schema']->shouldReceive('hasTable')->once()->with('migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('migrations', m::type('Closure'))->andReturnNull();

        $app['db']->shouldReceive('connection')->with(null)->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->andReturn($app['schema']);

        $stub = new Factory($app, $driver, $config);
        $model = $this->getMockModel();

        $this->assertNull($stub->runInstall($model, null));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::runUp()
     * method.
     *
     * @test
     */
    public function testRunUpMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [
            'path' => '/var/app/migrations',
        ];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[resolveMigrator]', [$app, $driver, $config])
                    ->shouldAllowMockingProtectedMethods();
        $model = $this->getMockModel();

        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');

        $stub->shouldReceive('resolveMigrator')->once()->andReturn($migrator);
        $migrator->shouldReceive('setConnection')->once()->with('primary')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('run')->once()->with('/var/app/migrations', false)->andReturnNull()
            ->shouldReceive('getNotes')->once()->andReturn([]);

        $this->assertNull($stub->runUp($model, 'primary'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::runDown()
     * method.
     *
     * @test
     */
    public function testRunDownMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [
            'path' => '/var/app/migrations',
        ];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[resolveMigrator]', [$app, $driver, $config])
            ->shouldAllowMockingProtectedMethods();
        $model = $this->getMockModel();

        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');

        $stub->shouldReceive('resolveMigrator')->once()->andReturn($migrator);
        $migrator->shouldReceive('setConnection')->once()->with('primary')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('rollback')->once()->with(false)->andReturnNull()
            ->shouldReceive('getNotes')->once()->andReturn([]);

        $this->assertNull($stub->runDown($model, 'primary'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::runReset()
     * method.
     *
     * @test
     */
    public function testRunResetMethod()
    {
        $app = $this->getAppContainer();
        $driver = 'user';
        $config = [
            'path' => '/var/app/migrations',
        ];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[resolveMigrator]', [$app, $driver, $config])
            ->shouldAllowMockingProtectedMethods();
        $model = $this->getMockModel();

        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');

        $stub->shouldReceive('resolveMigrator')->once()->andReturn($migrator);
        $migrator->shouldReceive('setConnection')->once()->with('primary')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('rollback')->once()->with(false)->andReturn(5)
            ->shouldReceive('rollback')->once()->with(false)->andReturn(0)
            ->shouldReceive('getNotes')->once()->andReturn([]);

        $this->assertNull($stub->runReset($model, 'primary'));
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function getMockModel()
    {
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $model->shouldReceive('getKey')->andReturn(5)
            ->shouldReceive('getTable')->andReturn('users')
            ->shouldReceive('toArray')->once()->andReturn(['name' => 'Administrator', 'username' => 'foo']);

        return $model;
    }

    /**
     * @return \Illuminate\Container\Container
     */
    protected function getAppContainer()
    {
        $app = new Container();
        $app['db'] = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['schema'] = m::mock('\Illuminate\Database\Schema\Builder');

        $app['db']->shouldReceive('connection')->with('primary')->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->andReturn($app['schema']);

        return $app;
    }
}
