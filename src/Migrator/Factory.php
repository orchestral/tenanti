<?php

namespace Orchestra\Tenanti\Migrator;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Tenanti\Contracts\Factory as FactoryContract;
use Orchestra\Tenanti\TenantiManager;

class Factory implements FactoryContract
{
    use Operation;

    /**
     * Construct a new migration manager.
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
     * @param  mixed|null  $id
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
     * @param  mixed|null  $id
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
     * @param  mixed|null  $id
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
     * @param  mixed|null  $id
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
     */
    public function runInstall(Model $entity, ?string $database): void
    {
        $table = $this->resolveMigrationTableName($entity);

        $this->migrator($table)
            ->usingConnection($this->asConnection($entity, $database), function ($migrator) use ($table) {
                $repository = $migrator->getRepository();

                if (! $repository->repositoryExists()) {
                    $repository->createRepository();

                    $this->note("<info>Migration table {$table} created successfully.</info>");
                }
            });
    }

    /**
     * Run migration up on a single entity.
     */
    public function runUp(Model $entity, ?string $database, bool $pretend = false): void
    {
        $this->migrator($this->resolveMigrationTableName($entity))
            ->outputUsing($this->notice)
            ->usingConnection($this->asConnection($entity, $database), function ($migrator) use ($entity, $pretend) {
                $migrator->setEntity($entity);

                $migrator->run($this->getMigrationPaths($entity), ['pretend' => (bool) $pretend]);
            });
    }

    /**
     * Run migration down on a single entity.
     */
    public function runDown(Model $entity, ?string $database, bool $pretend = false): void
    {
        $this->migrator($this->resolveMigrationTableName($entity))
            ->outputUsing($this->notice)
            ->usingConnection($this->asConnection($entity, $database), function ($migrator) use ($entity, $pretend) {
                $migrator->setEntity($entity);

                $migrator->rollback($this->getMigrationPaths($entity), ['pretend' => (bool) $pretend]);
            });
    }

    /**
     * Run migration reset on a single entity.
     */
    public function runReset(Model $entity, ?string $database, bool $pretend = false): void
    {
        $this->migrator($this->resolveMigrationTableName($entity))
            ->outputUsing($this->notice)
            ->usingConnection($this->asConnection($entity, $database), function ($migrator) use ($entity, $pretend) {
                $migrator->setEntity($entity);

                $migrator->reset($this->getMigrationPaths($entity), $pretend);
            });
    }
}
