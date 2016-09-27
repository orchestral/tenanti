<?php

namespace Orchestra\Tenanti\Jobs;

use Illuminate\Support\Arr;

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

        $database = Arr::get($this->config, 'database');
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
