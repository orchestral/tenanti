<?php

namespace Orchestra\Tenanti\Migrator;

use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Support\Str;
use Orchestra\Tenanti\TenantiManager;

class MigrationWriter extends MigrationCreator
{
    /**
     * Tenant manager.
     *
     * @var \Orchestra\Tenanti\TenantiManager
     */
    protected $tenant;

    /**
     * Create a new migration creator instance.
     */
    public function __construct(Filesystem $files, TenantiManager $tenant)
    {
        $this->files = $files;
        $this->tenant = $tenant;
    }

    /**
     * Write a migration file for tenant.
     */
    public function __invoke(
        ?string $driver,
        string $name,
        ?string $table,
        bool $create = false
    ): string {
        $migrator = $this->tenant->driver($driver);
        $path = $migrator->getMigrationPaths()[0] ?? null;

        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }

        if ($this->tenant->config("{$driver}.shared", true) === true) {
            $table = Str::replace($migrator->tablePrefix()."_{$table}", ['id' => '{$id}']);
        }

        $name = \implode('_', [$driver, 'tenant', $name]);

        return \pathinfo($this->create($name, $path, $table, $create), PATHINFO_FILENAME);
    }

    /**
     * Get the path to the stubs.
     */
    public function stubPath(): string
    {
        return __DIR__.'/stubs';
    }
}
