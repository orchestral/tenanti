<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Tenanti\TenantiManager;

class TenantiManagerTest extends \PHPUnit_Framework_TestCase
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
            'drivers' => ['user' => ['model' => 'User']],
            'chunk' => 100,
            'path'  => '/var/www/laravel/database/tenant/users',
        ];

        $expected = [
            'drivers' => [],
            'path'  => '/var/www/laravel/database/tenant/users',
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
            'chunk' => 100,
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
}
