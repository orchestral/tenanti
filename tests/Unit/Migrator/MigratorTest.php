<?php

namespace Orchestra\Tenanti\Tests\Unit\Migrator;

use Mockery as m;
use Illuminate\Support\Fluent;
use PHPUnit\Framework\TestCase;
use Orchestra\Tenanti\Migrator\Migrator;

class MigratorTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
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
        $resolver = m::mock('\Illuminate\Database\ConnectionResolver');
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
        $resolver = m::mock('\Illuminate\Database\ConnectionResolver');
        $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $migration = m::mock('FooMigration');

        $file = 'foo_migration.php';
        $batch = 5;
        $pretend = false;

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[getMigrationName,resolve,note]', [$repository, $resolver, $files])
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();

        $stub->path(realpath(__DIR__.'/../stubs'));
        $stub->setEntity($model);

        $stub->shouldReceive('getMigrationName')->once()->with($file)->andReturn($file)
            ->shouldReceive('resolve')->once()->with($file)->andReturn($migration)
            ->shouldReceive('note')->once()->with("<info>Migrated [foobar:10]:</info> $file")->andReturnNull();
        $model->shouldReceive('getKey')->once()->andReturn(10)
            ->shouldReceive('getTable')->once()->andReturn('foobar');
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
        $resolver = m::mock('\Illuminate\Database\ConnectionResolver');
        $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $instance = m::mock('FooMigration');

        $file = 'foo_migration.php';
        $batch = 5;
        $pretend = true;

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[getMigrationName,resolve,pretendToRun]', [$repository, $resolver, $files])
                    ->makePartial()
                    ->shouldAllowMockingProtectedMethods();

        $stub->path(realpath(__DIR__.'/../stubs'));
        $stub->setEntity($model);

        $stub->shouldReceive('getMigrationName')->once()->with($file)->andReturn($file)
            ->shouldReceive('resolve')->once()->with($file)->andReturn($instance)
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
        $resolver = m::mock('\Illuminate\Database\ConnectionResolver');
        $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $instance = m::mock('FooMigration');

        $file = 'foo_migration.php';
        $pretend = false;
        $migration = new Fluent(['migration' => $file]);

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[getMigrationName,resolve,note]', [$repository, $resolver, $files])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $stub->path(realpath(__DIR__.'/../stubs'));
        $stub->setEntity($model);

        $stub->shouldReceive('getMigrationName')->once()->with($file)->andReturn($file)
            ->shouldReceive('resolve')->once()->with($file)->andReturn($instance)
            ->shouldReceive('note')->once()->with("<info>Rolled back [foobar:10]:</info> $file")->andReturnNull();
        $model->shouldReceive('getKey')->once()->andReturn(10)
            ->shouldReceive('getTable')->once()->andReturn('foobar');
        $instance->shouldReceive('down')->once()->with(10, $model)->andReturnNull();
        $repository->shouldReceive('delete')->once()->with($migration)->andReturnNull();

        $this->assertNull($stub->runDown($file, $migration, $pretend));
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
        $resolver = m::mock('\Illuminate\Database\ConnectionResolver');
        $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $instance = m::mock('FooMigration');

        $file = 'foo_migration.php';
        $pretend = true;
        $migration = new Fluent(['migration' => $file]);

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[getMigrationName,resolve,pretendToRun]', [$repository, $resolver, $files])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $stub->path(realpath(__DIR__.'/../stubs'));
        $stub->setEntity($model);

        $stub->shouldReceive('getMigrationName')->once()->with($file)->andReturn($file)
            ->shouldReceive('resolve')->once()->with($file)->andReturn($instance)
            ->shouldReceive('pretendToRun')->once()->with($instance, 'down')->andReturnNull();

        $this->assertNull($stub->runDown($file, $migration, $pretend));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Migrator::getQueries()
     * method when pretending.
     *
     * @test
     */
    public function testGetQueriesMethodWhenPretending()
    {
        $repository = m::mock('\Illuminate\Database\Migrations\MigrationRepositoryInterface');
        $resolver = m::mock('\Illuminate\Database\ConnectionResolver');
        $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $instance = m::mock('FooMigration');

        $file = 'foo_migration.php';
        $batch = 5;
        $pretend = true;

        $stub = m::mock('\Orchestra\Tenanti\Migrator\Migrator[getMigrationName,resolve]', [$repository, $resolver, $files])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $stub->path(realpath(__DIR__.'/../stubs'));
        $stub->setEntity($model);

        $instance->shouldReceive('getConnection')->once()->andReturn('default')
            ->shouldReceive('up')->once()->with(15, $model)->andReturnNull();
        $resolver->shouldReceive('connection')->once()->with('default')->andReturnSelf()
            ->shouldReceive('pretend')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) {
                    $c();

                    return [['query' => 'SELECT * FROM `foobar`']];
                });
        $model->shouldReceive('getKey')->twice()->andReturn(15)
            ->shouldReceive('getTable')->once()->andReturn('foobar');
        $stub->shouldReceive('getMigrationName')->once()->with($file)->andReturn($file)
            ->shouldReceive('resolve')->once()->with($file)->andReturn($instance);

        $this->assertNull($stub->runUp($file, $batch, $pretend));
    }
}
