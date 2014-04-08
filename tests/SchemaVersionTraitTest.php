<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;

class SchemaVersionTraitTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test \Orchestra\Tenanti\SchemaVersionTrait::getSchemaVersionValue()
     * method.
     *
     * @test
     */
    public function testGetSchemaVersionValueMethod()
    {
        $stub = m::mock(__NAMESPACE__.'\SchemaVersionTraitStub[getAttribute]');
        $stub->shouldReceive('getAttribute')->once()->with('schema_version')->andReturn(3);

        $this->assertEquals(3, $stub->getSchemaVersionValue());
    }
}

class SchemaVersionTraitStub
{
    use \Orchestra\Tenanti\SchemaVersionTrait;

    public function getSchemaVersionKey()
    {
        return 'schema_version';
    }
}
