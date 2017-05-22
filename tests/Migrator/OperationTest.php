<?php

namespace Orchestra\Tenanti\TestCase\Migrator;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Tenanti\Migrator\Operation;

class OperationTest extends TestCase
{
    use Operation;

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::asDefaultDatabase()
     * method.
     *
     * @test
     */
    public function testAsDefaultDatabaseMethod()
    {
        $this->app = m::mock('\Illuminate\Container\Container[make]');
        $this->driver = 'user';

        $repository = new Repository([
            'database' => [
                'default'     => 'mysql',
                'connections' => [
                    'tenant' => [
                        'database' => 'tenants',
                    ],
                ],
            ],
        ]);

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$this->app]);

        $manager->shouldReceive('getConfig')->with('user.connection', null)->andReturn([
                    'template' => $repository->get('database.connections.tenant'),
                    'resolver' => function (Model $entity, array $template) {
                        return array_merge($template, [
                            'database' => "tenants_{$entity->getKey()}",
                        ]);
                    },
                    'name'    => 'tenant_{id}',
                    'options' => ['only' => ['user']],
                ]);

        $this->manager = $manager;

        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $this->app->shouldReceive('make')->twice()->with('config')->andReturn($repository);
        $model->shouldReceive('getKey')->twice()->andReturn(5)
            ->shouldReceive('toArray')->once()->andReturn([
                'id' => 5,
            ]);

        $this->assertEquals('tenant_5', $this->asDefaultConnection($model, 'tenant_{id}'));
        $this->assertEquals(['database' => 'tenants_5'], $repository->get('database.connections.tenant_5'));
        $this->assertEquals('tenant_5', $repository->get('database.default'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::asConnection()
     * method.
     *
     * @test
     */
    public function testAsConnectionMethod()
    {
        $this->app = m::mock('\Illuminate\Container\Container[make]');
        $this->driver = 'user';

        $repository = new Repository([
            'database' => [
                'default'     => 'mysql',
                'connections' => [
                    'tenant' => [
                        'database' => 'tenants',
                    ],
                ],
            ],
        ]);

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$this->app]);

        $manager->shouldReceive('getConfig')->with('user.connection', null)->andReturn([
                'template' => $repository->get('database.connections.tenant'),
                'resolver' => function (Model $entity, array $template) {
                    return array_merge($template, [
                        'database' => "tenants_{$entity->getKey()}",
                    ]);
                },
                'name'    => 'tenant_{id}',
                'options' => [],
            ]);

        $this->manager = $manager;

        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $this->app->shouldReceive('make')->once()->with('config')->andReturn($repository);
        $model->shouldReceive('getKey')->twice()->andReturn(5)
            ->shouldReceive('toArray')->once()->andReturn([
                'id' => 5,
            ]);

        $this->assertEquals('tenant_5', $this->asConnection($model, 'tenant_{id}'));
        $this->assertEquals(['database' => 'tenants_5'], $repository->get('database.connections.tenant_5'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::resolveModel()
     * method.
     *
     * @test
     */
    public function testResolveModelMethod()
    {
        $this->app = m::mock('\Illuminate\Container\Container[make]');
        $this->driver = 'user';

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$this->app]);

        $manager->shouldReceive('getConfig')->with('user.model', null)->andReturn('User')
            ->shouldReceive('getConfig')->with('user.database', null)->andReturnNull();

        $this->manager = $manager;

        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $this->app->shouldReceive('make')->once()->with('User')->andReturn($model);

        $model->shouldReceive('useWritePdo')->once()->andReturnSelf();

        $this->assertEquals($model, $this->getModel());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::resolveModel()
     * method with connection name.
     *
     * @test
     */
    public function testResolveModelMethodWithConnectionName()
    {
        $this->app = m::mock('\Illuminate\Container\Container[make]');
        $this->driver = 'user';

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$this->app]);

        $manager->shouldReceive('getConfig')->with('user.model', null)->andReturn('User')
            ->shouldReceive('getConfig')->with('user.database', null)->andReturn('primary');

        $this->manager = $manager;

        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $this->app->shouldReceive('make')->once()->with('User')->andReturn($model);

        $model->shouldReceive('setConnection')->once()->with('primary')->andReturnSelf()
            ->shouldReceive('useWritePdo')->once()->andReturnSelf();

        $this->assertEquals($model, $this->getModel());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::resolveModel()
     * method throw an exception when model is not an instance of
     * Eloquent.
     *
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function testResolveModelMethodThrowsException()
    {
        $this->app = m::mock('\Illuminate\Container\Container[make]');
        $this->driver = 'user';

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$this->app]);

        $manager->shouldReceive('getConfig')->with('user.model', null)->andReturn('User');

        $this->manager = $manager;

        $this->app->shouldReceive('make')->once()->with('User')->andReturnNull();

        $this->getModel();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::getModelName()
     * method.
     *
     * @test
     */
    public function testGetModelNameMethod()
    {
        $app = new Container();
        $this->driver = 'user';

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $manager->shouldReceive('getConfig')->with('user.model', null)->andReturn('User');

        $this->manager = $manager;

        $this->assertEquals('User', $this->getModelName());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::getMigrationPath()
     * method.
     *
     * @test
     */
    public function testGetMigrationPathMethod()
    {
        $this->driver = 'user';
        $path = realpath(__DIR__);
        $app = new Container();

        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $manager->shouldReceive('getConfig')->with('user.path', null)->andReturn($path);

        $this->manager = $manager;

        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('getKey')->andReturn(5);
        $this->loadMigrationsFrom('customPath', $model);
        $this->assertEquals([$path, 'customPath'], $this->getMigrationPath($model));

        $model2 = m::mock('\Illuminate\Database\Eloquent\Model');
        $model2->shouldReceive('getKey')->andReturn(6);
        $this->loadMigrationsFrom(['customPath', 'customPath2'], $model2);
        $this->assertEquals([$path, 'customPath', 'customPath2'], $this->getMigrationPath($model2));

        $model3 = m::mock('\Illuminate\Database\Eloquent\Model');
        $model3->shouldReceive('getKey')->andReturn(7);
        $this->assertEquals($path, $this->getMigrationPath($model3));

        $this->assertEquals($path, $this->getMigrationPath());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::getTablePrefix()
     * method.
     *
     * @test
     */
    public function testGetTablePrefixMethod()
    {
        $app = new Container();
        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $manager->shouldReceive('getConfig')->with('user.prefix', 'user')->andReturn('user');

        $this->driver = 'user';
        $this->manager = $manager;

        $this->assertEquals('user_{id}', $this->getTablePrefix());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Operation::getTablePrefix()
     * method.
     *
     * @test
     */
    public function testGetTablePrefixMethodWithDifferentPrefix()
    {
        $app = new Container();
        $manager = m::mock('\Orchestra\Tenanti\TenantiManager', [$app]);

        $manager->shouldReceive('getConfig')->with('user.prefix', 'user')->andReturn('member');

        $this->driver = 'user';
        $this->manager = $manager;

        $this->assertEquals('member_{id}', $this->getTablePrefix());
    }
}
