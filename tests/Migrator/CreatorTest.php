<?php

namespace Orchestra\Tenanti\TestCase\Migrator;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Orchestra\Tenanti\Migrator\Creator;

class CreatorTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
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
        $files = m::mock('\Illuminate\Filesystem\Filesystem');

        $stub = new Creator($files);

        $this->assertContains('src/Migrator/stubs', $stub->stubPath());
    }
}
