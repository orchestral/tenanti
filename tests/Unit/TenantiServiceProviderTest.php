<?php

namespace Orchestra\Tenanti\Tests\Unit;

use Orchestra\Tenanti\TenantiServiceProvider;
use PHPUnit\Framework\TestCase;

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
