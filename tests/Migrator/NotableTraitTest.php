<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Orchestra\Tenanti\Migrator\NotableTrait;

class NotableTraitTest extends \PHPUnit_Framework_TestCase
{
    use NotableTrait;

    /**
     * Test Orchestra\Tenanti\Migrator\NotableTrait::getNotes()
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
     * Test Orchestra\Tenanti\Migrator\NotableTrait::flushNotes()
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
