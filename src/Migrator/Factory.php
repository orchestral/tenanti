<?php

namespace Orchestra\Tenanti\Migrator;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Tenanti\Contracts\Factory as FactoryContract;
use Orchestra\Tenanti\TenantiManager;

class Factory implements FactoryContract
{
    use Notable, Operation;

    /**
     * Construct a new migration manager.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  \Orchestra\Tenanti\TenantiManager  $manager
     * @param  string  $driver
     */
    public function __construct(Container $app, TenantiManager $manager, string $driver)
    {
        $this->app = $app;
        $this->manager = $manager;
        $this->driver = $driver;
    }

    /**
     * Install migrations.
     *
     * @param  string|null  $database
     * @param  mixed|null  $id
     *
     * @return void
     */
    public function install(?string $database, $id = null): void
    {
        if (! \is_null($id)) {
            $this->executeFor($id, function ($entity) use ($database) {
                $this->runInstall($entity, $database);
            });

            return;
        }

        $this->executeForEach(function ($entity) use ($database) {
            $this->runInstall($entity, $database);
        });
    }

    /**
     * Run migrations.
     *
     * @param  string|null  $database
     * @param  mixed|null  $id
     * @param  bool  $pretend
     *
     * @return void
     */
    public function run(?string $database, $id = null, bool $pretend = false): void
    {
        if (! \is_null($id)) {
            $this->executeFor($id, function ($entity) use ($database, $pretend) {
                $this->runUp($entity, $database, $pretend);
            });

            return;
        }

        $this->executeForEach(function ($entity) use ($database, $pretend) {
            $this->runUp($entity, $database, $pretend);
        });
    }

    /**
     * Rollback migrations.
     *
     * @param  string|null  $database
     * @param  mixed|null  $id
     * @param  bool  $pretend
     *
     * @return void
     */
    public function rollback(?string $database, $id = null, bool $pretend = false): void
    {
        if (! \is_null($id)) {
            $this->executeFor($id, function ($entity) use ($database, $pretend) {
                $this->runDown($entity, $database, $pretend);
            });

            return;
        }

        $this->executeForEach(function ($entity) use ($database, $pretend) {
            $this->runDown($entity, $database, $pretend);
        });
    }

    /**
     * Reset migrations.
     *
     * @param  string|null  $database
     * @param  mixed|null  $id
     * @param  bool  $pretend
     *
     * @return void
     */
    public function reset(?string $database, $id = null, bool $pretend = false): void
    {
        if (! \is_null($id)) {
            $this->executeFor($id, function ($entity) use ($database, $pretend) {
                $this->runReset($entity, $database, $pretend);
            });

            return;
        }

        $this->executeForEach(function ($entity) use ($database, $pretend) {
            $this->runReset($entity, $database, $pretend);
        });
    }

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     *
     * @return void
     */
    public function runInstall(Model $entity, ?string $database): void
    {
        $database = $this->asConnection($entity, $database);
        $table = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigrator($table);
        $repository = $migrator->getRepository();

        $migrator->setConnection($database);

        if (! $repository->repositoryExists()) {
            $repository->createRepository();

            $this->note("<info>Migration table {$table} created successfully.</info>");
        }

        $migrator->resetConnection();
    }

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runUp(Model $entity, ?string $database, bool $pretend = false): void
    {
        $database = $this->asConnection($entity, $database);
        $table = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigratorWithNotes($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);

        $migrator->run($this->getMigrationPaths($entity), ['pretend' => (bool) $pretend]);
        $migrator->resetConnection();
    }

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runDown(Model $entity, ?string $database, bool $pretend = false): void
    {
        $database = $this->asConnection($entity, $database);
        $table = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigratorWithNotes($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);

        $migrator->rollback($this->getMigrationPaths($entity), ['pretend' => (bool) $pretend]);
        $migrator->resetConnection();
    }

    /**
     * Run migration reset on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runReset(Model $entity, ?string $database, bool $pretend = false): void
    {
        $database = $this->asConnection($entity, $database);
        $table = $this->resolveMigrationTableName($entity);
        $migrator = $this->resolveMigratorWithNotes($table);

        $migrator->setConnection($database);
        $migrator->setEntity($entity);
        $migrator->reset($this->getMigrationPaths($entity), $pretend);
        $migrator->resetConnection();
    }
}
