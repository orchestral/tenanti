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
        $stub = new SchemaVersionInterfaceStub;

        $this->assertEquals('schema_version', $stub->getSchemaVersionKey());
    }
}

class SchemaVersionInterfaceStub implements SchemaVersionInterface
{
    public function getSchemaVersionKey()
    {
        return 'schema_version';
    }

    public function getSchemaVersionValue()
    {
        return 1;
    }
}
