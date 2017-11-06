<?php

namespace Orchestra\Tenanti\Jobs;

use RuntimeException;

class CreateTenant extends Job
{
    /**
     * Fire queue on creating a model.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shouldBeFailed()) {
            return;
        }

        $database = $this->config['database'] ?? null;
        $migrator = $this->resolveMigrator();

        if (is_null($this->model) && $this->job) {
            return $this->release(10);
        }

        $migrator->runInstall($this->model, $database, $id);
        $migrator->runUp($this->model, $database);

        $this->delete();
    }
}
