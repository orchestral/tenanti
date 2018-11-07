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
    public function tearDown()
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
        $stub->setConfig($config);

        $resolver = $stub->driver('user');

        $this->assertInstanceOf('\Orchestra\Tenanti\Migrator\Factory', $resolver);
        $this->assertEquals($expected, $stub->getConfig());
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::driver() method
     * when driver is not available.
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedMessage Driver [user] not supported.
     */
    public function testDriverMethodGivenDriverNotAvailable()
    {
        $app = new Container();

        $config = [
            'drivers' => [],
        ];

        with(new TenantiManager($app))->setConfig($config)->driver('user');
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::getDefaultDriver()
     * is not implemented.
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedMessage Default driver not implemented.
     */
    public function testGetDefaultDriverIsNotImplemented()
    {
        (new TenantiManager(null))->driver();
    }

    /**
     * Test Orchestra\Tenanti\TenantiManager::config() method.
     *
     * @test
     */
    public function testConfigMethod()
    {
        $app = new Container();

        $config = [
            'drivers' => [
                'user' => ['model' => 'User', 'path' => '/var/www/laravel/database/tenant/users'],
            ],
            'connection' => null,
        ];

        $stub = new TenantiManager($app);

        $this->assertSame([], $stub->config());

        $stub->setConfig($config);

        $this->assertEquals($config, $stub->config());
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
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Database connection [foo] is not available.
     */
    public function testConnectionMethodGivenConfigTemplateDoesNotExists()
    {
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
