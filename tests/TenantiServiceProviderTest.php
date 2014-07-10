<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;
use Orchestra\Tenanti\TenantiServiceProvider;

class TenantiServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider is not deferred.
     *
     * @test
     */
    public function testServiceProviderIsDeferred()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertFalse($stub->isDeferred());
    }

    /**
     * Test \Orchestra\Tenanti\TenantiServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertNull($stub->register());
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
