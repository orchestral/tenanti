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

        if (is_null($this->model)) {
            throw new RuntimeException("Missing model");
        }

        $id = $this->model->getKey();

        $migrator->reset($database, $id);

        $this->delete();
    }
}
