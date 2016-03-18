<?php namespace Orchestra\Tenanti\Jobs;

use Illuminate\Support\Arr;

class DeleteTenant extends Job
{
    /**
     * Fire queue on deleting a model.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            return $this->failed();
        }

        $database = Arr::get($this->config, 'database');
        $migrator = $this->resolveMigrator();

        if (is_null($this->model)) {
            return $this->release(10);
        }

        $migrator->runReset($this->model, $database);

        $this->delete();
    }
}
