<?php

namespace Orchestra\Tenanti\Tests\Integration;

use Orchestra\Tenanti\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_required_services()
    {
        $this->assertInstanceOf('\Orchestra\Tenanti\TenantiManager', resolve('orchestra.tenanti'));
    }

    /** @test */
    public function it_boot_tenanti_configuration()
    {
        $tenanti = resolve('orchestra.tenanti');

        $this->assertSame('App\User', $tenanti->getConfig('drivers.user.model'));
    }
}
