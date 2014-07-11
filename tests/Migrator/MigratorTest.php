<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Illuminate\Support\Fluent;
use Mockery as m;
use Orchestra\Tenanti\Migrator\Migrator;

class MigratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Migrator::setEntity()
     * method.
     *
     * @test
     */
    public function testSetEntityMethod()
    {
        $repository = m::mock('\Illuminate\Database\Migrations\MigrationRepositoryInterface');
        $resolver = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $stub = new Migrator($repository, $resolver, $files);

        $this->assertEquals($stub, $stub->setEntity($model));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Migrator::runUp()
     * method.
     *
     * @test
     */
    public function testRunUpMethod()
    {
        $repository = m::mock('\Illuminate\Database\Migrations\MigrationRepositoryInterface');
        $resolver   = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $files      = m::mock('\Illuminate\Filesystem\Filesystem');
        $model      = m::mock('\Illuminate\Database\Eloquent\Model');
        $migration  = m::mock('FooMigration');

        $file    = 'foo_migration.php';
        $batch   = 5;
        $pretend = false;

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[resolve,note]', array($repository, $resolver, $files))
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
        $stub->setEntity($model);

        $stub->shouldReceive('resolve')->once()->with($file)->andReturn($migration)
            ->shouldReceive('note')->once()->with("<info>Migrated:</info> $file")->andReturnNull();
        $model->shouldReceive('getKey')->once()->andReturn(10);
        $migration->shouldReceive('up')->once()->with(10, $model)->andReturnNull();
        $repository->shouldReceive('log')->once()->with($file, $batch)->andReturnNull();

        $this->assertNull($stub->runUp($file, $batch, $pretend));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Migrator::runUp()
     * method when pretending.
     *
     * @test
     */
    public function testRunUpMethodWhenPretending()
    {
        $repository = m::mock('\Illuminate\Database\Migrations\MigrationRepositoryInterface');
        $resolver   = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $files      = m::mock('\Illuminate\Filesystem\Filesystem');
        $model      = m::mock('\Illuminate\Database\Eloquent\Model');
        $instance   = m::mock('FooMigration');

        $file    = 'foo_migration.php';
        $batch   = 5;
        $pretend = true;

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[resolve,pretendToRun]', array($repository, $resolver, $files))
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();
        $stub->setEntity($model);

        $stub->shouldReceive('resolve')->once()->with($file)->andReturn($instance)
            ->shouldReceive('pretendToRun')->once()->with($instance, 'up')->andReturnNull();

        $this->assertNull($stub->runUp($file, $batch, $pretend));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Migrator::runDown()
     * method.
     *
     * @test
     */
    public function testRunDownMethod()
    {
        $repository = m::mock('\Illuminate\Database\Migrations\MigrationRepositoryInterface');
        $resolver   = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $files      = m::mock('\Illuminate\Filesystem\Filesystem');
        $model      = m::mock('\Illuminate\Database\Eloquent\Model');
        $instance   = m::mock('FooMigration');

        $file      = 'foo_migration.php';
        $pretend   = false;
        $migration = new Fluent(array('migration' => $file));

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[resolve,note]', array($repository, $resolver, $files))
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $stub->setEntity($model);

        $stub->shouldReceive('resolve')->once()->with($file)->andReturn($instance)
            ->shouldReceive('note')->once()->with("<info>Rolled back:</info> $file")->andReturnNull();
        $model->shouldReceive('getKey')->once()->andReturn(10);
        $instance->shouldReceive('down')->once()->with(10, $model)->andReturnNull();
        $repository->shouldReceive('delete')->once()->with($migration)->andReturnNull();

        $this->assertNull($stub->runDown($migration, $pretend));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Migrator::runDown()
     * method when pretending.
     *
     * @test
     */
    public function testRunDownMethodWhenPretending()
    {
        $repository = m::mock('\Illuminate\Database\Migrations\MigrationRepositoryInterface');
        $resolver   = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $files      = m::mock('\Illuminate\Filesystem\Filesystem');
        $model      = m::mock('\Illuminate\Database\Eloquent\Model');
        $instance  = m::mock('FooMigration');

        $file      = 'foo_migration.php';
        $pretend   = true;
        $migration = new Fluent(array('migration' => $file));

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[resolve,pretendToRun]', array($repository, $resolver, $files))
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $stub->setEntity($model);

        $stub->shouldReceive('resolve')->once()->with($file)->andReturn($instance)
            ->shouldReceive('pretendToRun')->once()->with($instance, 'down')->andReturnNull();

        $this->assertNull($stub->runDown($migration, $pretend));
    }
}
