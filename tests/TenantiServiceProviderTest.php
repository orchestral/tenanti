<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;
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
        $app  = m::mock('\Illuminate\Container\Container[bindShared]');

        $app->shouldReceive('bindShared')->once()->with('orchestra.tenanti', m::type('Closure'))
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
        $stub = m::mock('\Orchestra\Tenanti\TenantiServiceProvider[package]', [null]);
        $path = realpath(__DIR__ . '/../src/');

        $stub->shouldReceive('package')->once()->with('orchestra/tenanti', 'orchestra/tenanti', $path)->andReturnNull();

        $this->assertNull($stub->boot());
    }
}
