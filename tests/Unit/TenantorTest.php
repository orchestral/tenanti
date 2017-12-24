<?php

namespace Orchestra\Tenanti\Tests\Unit;

use Orchestra\Tenanti\Tenantor;
use PHPUnit\Framework\TestCase;

class TenantorTest extends TestCase
{
    /** @test */
    public function it_can_be_initiated()
    {
        $stub = Tenantor::make('company', 5, 'primary');

        $this->assertSame('company', $stub->getTenantName());
        $this->assertSame(5, $stub->getTenantKey());
        $this->assertSame('primary', $stub->getTenantConnectionName());
    }
}
