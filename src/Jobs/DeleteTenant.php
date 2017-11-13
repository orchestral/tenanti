<?php

namespace Orchestra\Tenanti\Jobs;

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

        if (! $this->shouldBeDelayed()) {
            $migrator->runReset($this->model, $database);

            $this->delete();
        }
    }
}
