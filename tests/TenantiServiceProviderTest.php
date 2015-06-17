<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Orchestra\Tenanti\TenantiServiceProvider;

class TenantiServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider is deferred.
     *
     * @test
     */
    public function testServiceProviderIsDeferred()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertTrue($stub->isDeferred());
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $app = m::mock('\Illuminate\Container\Container[singleton]', '\ArrayAccess');
        $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $app->instance('config', $config);
        $config->shouldReceive('get')->once()->with('orchestra.tenanti')->andReturn([]);

        $app->shouldReceive('singleton')->once()->with('orchestra.tenanti', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($app) {
                    $app[$n] = $c($app);
                });

        $stub = new TenantiServiceProvider($app);

        $this->assertNull($stub->register());
        $this->assertInstanceOf('\Orchestra\Tenanti\TenantiManager', $app['orchestra.tenanti']);
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app = m::mock('\Illuminate\Contracts\Container\Container');
        $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $app->shouldReceive('make')->once()->with('config')->andReturn($config);

        $stub = m::mock('\Orchestra\Tenanti\TenantiServiceProvider[addConfigComponent,bootUsingLaravel]', [$app])
                    ->shouldAllowMockingProtectedMethods();
        $path = realpath(__DIR__.'/../resources');

        $stub->shouldReceive('addConfigComponent')->once()
                ->with('orchestra/tenanti', 'orchestra/tenanti', $path.'/config')->andReturnNull()
            ->shouldReceive('bootUsingLaravel')->once()->with($path)->andReturnNull();

        $this->assertNull($stub->boot());
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::bootWithLaravel()
     * method.
     *
     * @test
     */
    public function testBootWithLaravelMethod()
    {
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');
        Container::setInstance($app);

        $stub = m::mock('\Orchestra\Tenanti\TenantiServiceProvider[addConfigComponent,hasPackageRepository,mergeConfigFrom,publishes]', [$app])
                    ->shouldAllowMockingProtectedMethods();
        $path = realpath(__DIR__.'/../resources');

        $app->shouldReceive('make')->once()->with('path.config')->andReturn('/var/www/config');

        $stub->shouldReceive('addConfigComponent')->once()
                ->with('orchestra/tenanti', 'orchestra/tenanti', $path.'/config')->andReturnNull()
            ->shouldReceive('hasPackageRepository')->once()->andReturn(false)
            ->shouldReceive('mergeConfigFrom')->once()
                ->with($path.'/config/config.php', 'orchestra.tenanti')->andReturnNull()
            ->shouldReceive('publishes')
                ->with([
                    $path.'/config/config.php' => '/var/www/config/orchestra/tenanti.php'
                ])->andReturnNull();

        $this->assertNull($stub->boot());
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertContains('orchestra.tenanti', $stub->provides());
    }
}
