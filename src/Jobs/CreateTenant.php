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

        if (is_null($this->model)) {
            return $this->release(10);
        }

        $id = $this->model->getKey();

        $migrator->install($database, $id);
        $migrator->run($database, $id);

        $this->delete();
    }
}
