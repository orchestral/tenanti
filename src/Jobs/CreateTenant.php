<?php

namespace Orchestra\Tenanti\Jobs;

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

        if (! $this->shouldBeDelayed()) {
            $migrator->runInstall($this->model, $database);
            $migrator->runUp($this->model, $database);

            $this->delete();
        }
    }
}
