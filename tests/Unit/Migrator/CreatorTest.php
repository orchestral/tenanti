<?php

namespace Orchestra\Tenanti\Tests\Unit\Migrator;

use Mockery as m;
use Orchestra\Tenanti\Migrator\Creator;
use PHPUnit\Framework\TestCase;

class CreatorTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Creator::getStubPath()
     * method.
     *
     * @test
     */
    public function testGetStubPath()
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');

        $stub = new Creator($files);

        $path = \realpath(__DIR__.'/../../../');

        $this->assertStringContainsString($path.\DIRECTORY_SEPARATOR.'src/Migrator/stubs', $stub->stubPath());
    }
}
