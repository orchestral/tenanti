<?php

namespace Orchestra\Tenanti\Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Orchestra\Tenanti\TenantiServiceProvider;

class TenantiServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registered_as_deferred()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertTrue($stub->isDeferred());
    }

    /** @test */
    public function it_has_required_provides()
    {
        $stub = new TenantiServiceProvider(null);

        $this->assertContains('orchestra.tenanti', $stub->provides());
    }
}
