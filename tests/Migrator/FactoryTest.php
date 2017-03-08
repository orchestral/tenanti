<?php

namespace Orchestra\Tenanti\TestCase\Migrator;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Orchestra\Tenanti\Migrator\Factory;

class FactoryTest extends TestCase
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runInstall]', [$app, $manager, $driver]);
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
        $db = $app['db'];
        $driver = 'user';

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runInstall]', [$app, $manager, $driver]);
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');
        $model = $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();

        $entities = [$entity];

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->andReturnNull();
        $migrator->shouldReceive('setConnection')->once()->with('foo')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('run')->once()->with(null, ['pretend' => false])->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();
        $model->shouldReceive('getKey')->andReturn(5)
            ->shouldReceive('toArray')->andReturn([])
            ->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');
        $model = $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->andReturnNull();
        $migrator->shouldReceive('setConnection')->once()->with('foo')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('run')->once()->with(null, ['pretend' => false])->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();
        $model->shouldReceive('getKey')->andReturn(5)
            ->shouldReceive('toArray')->andReturn([])
            ->shouldReceive('newQuery->findOrFail')->once()->with(10)
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');
        $model = $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $entities = [$entity];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->andReturnNull();
        $migrator->shouldReceive('setConnection')->once()->with('foo')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('rollback')->once()->with(null, ['pretend' => false])->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();
        $model->shouldReceive('getKey')->andReturn(5)
            ->shouldReceive('toArray')->andReturn([])
            ->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');
        $model = $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->andReturnNull();
        $migrator->shouldReceive('setConnection')->once()->with('foo')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('rollback')->once()->with(null, ['pretend' => false])->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();
        $model->shouldReceive('getKey')->andReturn(5)
            ->shouldReceive('toArray')->andReturn([])
            ->shouldReceive('newQuery->findOrFail')->once()->with(10)
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');
        $model = $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $entities = [$entity];

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->andReturnNull();
        $migrator->shouldReceive('setConnection')->once()->with('foo')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('reset')->once()->with(null, false)->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();
        $model->shouldReceive('getKey')->andReturn(5)
            ->shouldReceive('toArray')->andReturn([])
            ->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');
        $model = $entity = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->andReturnNull();
        $migrator->shouldReceive('setConnection')->once()->with('foo')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('reset')->once()->with(null, false)->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();
        $model->shouldReceive('getKey')->andReturn(5)
            ->shouldReceive('toArray')->andReturn([])
            ->shouldReceive('newQuery->findOrFail')->once()->with(10)
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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $stub = new Factory($app, $manager, $driver);

        $app['schema']->shouldReceive('hasTable')->once()->with('user_5_migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('user_5_migrations', m::type('Closure'))->andReturnNull();

        $model = $this->getMockModel();

        $manager->shouldReceive('getConfig')->with('user.connection', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.migration', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.shared', true)->andReturn(true)
            ->shouldReceive('getConfig')->with('user.prefix', 'user')->andReturn('user');

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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $app['schema']->shouldReceive('hasTable')->once()->with('migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('migrations', m::type('Closure'))->andReturnNull();

        $app['db']->shouldReceive('connection')->with('tenant_foo')->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->andReturn($app['schema']);

        $stub = new Factory($app, $manager, $driver);
        $model = $this->getMockModel();

        $manager->shouldReceive('getConfig')->with('user.path', null)->andReturn('/var/app/migrations')
            ->shouldReceive('getConfig')->with('user.connection', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.shared', true)->andReturn(true)
            ->shouldReceive('getConfig')->with('user.migration', null)->andReturn('migrations');

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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $app['schema']->shouldReceive('hasTable')->once()->with('migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('migrations', m::type('Closure'))->andReturnNull();
        $app['db']->shouldReceive('connection')->with(null)->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->andReturn($app['schema']);

        $stub = new Factory($app, $manager, $driver);

        $model = $this->getMockModel();

        $manager->shouldReceive('getConfig')->with('user.path', null)->andReturn('/var/app/migrations')
            ->shouldReceive('getConfig')->with('user.connection', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.shared', true)->andReturn(true)
            ->shouldReceive('getConfig')->with('user.migration', null)->andReturn('migrations');

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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();
        $model = $this->getMockModel();

        $stub->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->with('user.path', null)->andReturn('/var/app/migrations')
            ->shouldReceive('getConfig')->with('user.connection', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.migration', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.shared', true)->andReturn(true)
            ->shouldReceive('getConfig')->with('user.prefix', 'user')->andReturn('user');
        $migrator->shouldReceive('setConnection')->once()->with('primary')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('run')->once()->with(['/var/app/migrations'], ['pretend' => false])->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();

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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();
        $model = $this->getMockModel();

        $stub->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->with('user.path', null)->andReturn('/var/app/migrations')
            ->shouldReceive('getConfig')->with('user.connection', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.migration', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.shared', true)->andReturn(true)
            ->shouldReceive('getConfig')->with('user.prefix', 'user')->andReturn('user');
        $migrator->shouldReceive('setConnection')->once()->with('primary')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('rollback')->with(['/var/app/migrations'], ['pretend' => false])->andReturnNull()
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();

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

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);
        $migrator = m::mock('\Orchestra\Tenanti\Migrator\Migrator');

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Factory[resolveMigrator]', [$app, $manager, $driver])
                    ->shouldAllowMockingProtectedMethods();
        $model = $this->getMockModel();

        $stub->shouldReceive('resolveMigrator')->once()->andReturn($migrator);

        $manager->shouldReceive('getConfig')->with('user.path', null)->andReturn('/var/app/migrations')
            ->shouldReceive('getConfig')->with('user.connection', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.migration', null)->andReturnNull()
            ->shouldReceive('getConfig')->with('user.shared', true)->andReturn(true)
            ->shouldReceive('getConfig')->with('user.prefix', 'user')->andReturn('user');
        $migrator->shouldReceive('setConnection')->once()->with('primary')->andReturnNull()
            ->shouldReceive('setEntity')->once()->with($model)->andReturnNull()
            ->shouldReceive('reset')->once()->with(['/var/app/migrations'], false)->andReturn(5)
            ->shouldReceive('resetConnection')->once()->with()->andReturnNull();

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
            ->shouldReceive('toArray')->andReturn(['name' => 'Administrator', 'username' => 'foo']);

        return $model;
    }

    /**
     * @return \Illuminate\Container\Container
     */
    protected function getAppContainer()
    {
        $app = new Container();
        $app['config'] = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['db'] = m::mock('\Illuminate\Database\ConnectionResolver');
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['schema'] = m::mock('\Illuminate\Database\Schema\Builder');

        $app['db']->shouldReceive('connection')->with('primary')->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->andReturn($app['schema']);

        return $app;
    }
}
