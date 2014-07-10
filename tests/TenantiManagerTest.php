<?php namespace Orchestra\Tenanti\TestCase;

use Orchestra\Tenanti\TenantiManager;

class TenantiManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Orchestra\Tenanti\TenantiManager::getDefaultDriver()
     * is not implemented.
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedMessage Default driver not implemented.
     */
    public function testGetDefaultDriverIsNotImplemented()
    {
        (new TenantiManager(null))->driver();
    }
}
