<?php

namespace Orchestra\Tenanti\Tests\Unit\Migrator;

use Mockery as m;
use Orchestra\Tenanti\Migrator\MigrationWriter;
use PHPUnit\Framework\TestCase;

class MigrationWriterTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\MigrationWriter::getStubPath()
     * method.
     *
     * @test
     */
    public function testGetStubPath()
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $tenanti = m::mock('Orchestra\Tenanti\TenantiManager');

        $stub = new MigrationWriter($files, $tenanti);

        $path = \realpath(__DIR__.'/../../../');

        $this->assertStringContainsString($path.\DIRECTORY_SEPARATOR.'src'.\DIRECTORY_SEPARATOR.'Migrator/stubs', $stub->stubPath());
    }
}
