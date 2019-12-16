<?php

namespace Orchestra\Tenanti\Migrator;

use Illuminate\Support\Str;
use Orchestra\Tenanti\TenantiManager;

class MigrationWriter
{
    protected $tenant;
    protected $creator;

    /**
     * Construct a new migration writer.
     */
    public function __construct(TenantiManager $tenant, Creator $creator)
    {
        $this->tenant = $tenant;
        $this->creator = $creator;
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
        $files = $this->creator->getFilesystem();
        $path = $migrator->getMigrationPaths()[0] ?? null;

        if (! $files->isDirectory($path)) {
            $files->makeDirectory($path, 0755, true);
        }

        if ($this->tenant->config("{$driver}.shared", true) === true) {
            $table = Str::replace($migrator->getTablePrefix()."_{$table}", ['id' => '{$id}']);
        }

        $name = \implode('_', [$driver, 'tenant', $name]);

        return \pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);
    }
}
