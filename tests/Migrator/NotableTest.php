<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Orchestra\Tenanti\Migrator\Notable;

class NotableTest extends \PHPUnit_Framework_TestCase
{
    use Notable;

    /**
     * Test Orchestra\Tenanti\Migrator\Notable::getNotes()
     * method.
     *
     * @test
     */
    public function testGetNotesMethod()
    {
        $this->notes = $expected = ['foobar'];

        $this->assertEquals($expected, $this->getNotes());
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Notable::flushNotes()
     * method.
     *
     * @test
     */
    public function testFlushNotesMethod()
    {
        $this->notes = $expected = ['foobar'];

        $this->assertEquals($expected, $this->getNotes());

        $this->flushNotes();

        $this->assertEquals([], $this->getNotes());
    }
}
