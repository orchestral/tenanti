<?php namespace Orchestra\Tenanti\TestCase;

use Mockery as m;
use Orchestra\Tenanti\DatabaseMigrationRepository;

class DatabaseMigrationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test getter and setter for
     * Orchestra\Tenanti\DatabaseMigrationRepository::$table.
     *
     * @test
     */
    public function testGetterAndSetterForTableProperty()
    {
        $resolver = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $stub = new DatabaseMigrationRepository($resolver, 'migrations');

        $this->assertEquals('migrations', $stub->getTable());

        $stub->setTable('foo_migrations');
        $this->assertEquals('foo_migrations', $stub->getTable());
    }
}
