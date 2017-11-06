<?php

namespace Orchestra\Tenanti\Jobs;

use RuntimeException;

class DeleteTenant extends Job
{
    /**
     * Fire queue on deleting a model.
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

        $migrator->runReset($this->model, $database);

        $this->delete();
    }
}
