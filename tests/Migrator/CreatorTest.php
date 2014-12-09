<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Mockery as m;
use Orchestra\Tenanti\Migrator\Creator;

class CreatorTest extends \PHPUnit_Framework_TestCase
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

        $this->assertContains('src/Migrator/stubs', $stub->getStubPath());
    }
}
