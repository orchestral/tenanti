<?php

namespace Orchestra\Tenanti\Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Orchestra\Tenanti\TenantiManager;

class TenantiManagerTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::driver() method.
     *
     * @test
     */
    public function testDriverMethod()
    {
        $app = new Container();
        $app->instance('config', m::mock('Illuminate\Contracts\Config\Repository'));

        $config = [
            'drivers' => [
                'user' => ['model' => 'User', 'path' => '/var/www/laravel/database/tenant/users'],
            ],
        ];

        $expected = [
            'drivers' => [],
            'user' => [
                'path' => '/var/www/laravel/database/tenant/users',
                'connection' => null,
                'model' => 'User',
            ],
            'connection' => null,
        ];

        $stub = new TenantiManager($app);
        $stub->setConfiguration($config);

        $resolver = $stub->driver('user');

        $this->assertInstanceOf('\Orchestra\Tenanti\Migrator\Factory', $resolver);
        $this->assertEquals($expected, $stub->getConfig());
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::driver() method
     * when driver is not available.
     *
     * @test
     */
    public function testDriverMethodGivenDriverNotAvailable()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Driver [user] not supported');

        $container = new Container();
        $container->instance('config', m::mock('Illuminate\Contracts\Config\Repository'));

        $config = [
            'drivers' => [],
        ];

        with(new TenantiManager($container))->setConfiguration($config)->driver('user');
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::getDefaultDriver()
     * is not implemented.
     *
     * @test
     */
    public function testGetDefaultDriverIsNotImplemented()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Default driver not implemented.');

        $container = m::mock('Illuminate\Contracts\Container\Container');
        $container->shouldReceive('make')->once()->with('config')->andReturn(m::mock('Illuminate\Contracts\Config\Repository'));

        (new TenantiManager($container))->driver();
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::config() method.
     *
     * @test
     */
    public function testConfigMethod()
    {
        $app = new Container();
        $app->instance('config', m::mock('Illuminate\Contracts\Config\Repository'));

        $config = [
            'drivers' => [
                'user' => ['model' => 'User', 'path' => '/var/www/laravel/database/tenant/users'],
            ],
            'connection' => null,
        ];

        $stub = new TenantiManager($app);

        $this->assertSame([], $stub->getConfiguration());

        $stub->setConfiguration($config);

        $this->assertEquals($config, $stub->getConfiguration());
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::connection() method.
     *
     * @test
     */
    public function testConnectionMethod()
    {
        $app = new Container();
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()->with('database.connections.tenant', null)
            ->andReturn([
                'database' => 'tenant',
            ]);

        $callback = function () {
            return ['database' => 'tenant_5'];
        };

        $expected = [
            'template' => ['database' => 'tenant'],
            'resolver' => $callback,
            'name' => 'tenant_{id}',
            'options' => [],
        ];

        $stub = new TenantiManager($app);
        $stub->connection('tenant', $callback);

        $this->assertEquals(['connection' => $expected], $stub->getConfig());
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::connection() method
     * using default connection.
     *
     * @test
     */
    public function testConnectionMethodWithDefaultConnection()
    {
        $app = new Container();
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()->with('database.default')->andReturn('mysql')
            ->shouldReceive('get')->once()->with('database.connections.mysql', null)
                ->andReturn([
                    'database' => 'tenant',
                ]);

        $callback = function () {
            return ['database' => 'tenant_5'];
        };

        $expected = [
            'template' => ['database' => 'tenant'],
            'resolver' => $callback,
            'name' => 'mysql_{id}',
            'options' => [],
        ];

        $stub = new TenantiManager($app);
        $stub->connection(null, $callback);

        $this->assertEquals(['connection' => $expected], $stub->getConfig());
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::connection() method
     * given configuration template doesn't exists.
     *
     * @test
     */
    public function testConnectionMethodGivenConfigTemplateDoesNotExists()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Database connection [foo] is not available.');

        $app = new Container();
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()->with('database.connections.foo', null)->andReturnNull();

        $callback = function () {
            return ['database' => 'tenant_5'];
        };

        $stub = new TenantiManager($app);
        $stub->connection('foo', $callback);
    }
}
