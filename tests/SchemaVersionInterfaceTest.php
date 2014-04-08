<?php namespace Orchestra\Tenanti\TestCase;

use Orchestra\Tenanti\SchemaVersionInterface;

class SchemaVersionInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test \Orchestra\Tenanti\SchemaVersionInterface::getSchemaVersionKey()
     * method.
     *
     * @test
     */
    public function testGetSchemaVersionKeyMethod()
    {
        $stub = new SchemaVersionStub;

        $this->assertEquals('schema_version', $stub->getSchemaVersionKey());
    }
}

class SchemaVersionStub implements SchemaVersionInterface
{
    public function getSchemaVersionKey()
    {
        return 'schema_version';
    }
}
