<?php namespace Orchestra\Tenanti\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Queue\Job;

class DeleteTenant extends Tenant
{
    /**
     * Run queue on deleting a model.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  array  $data
     *
     * @return void
     */
    public function fire(Job $job, array $data)
    {
        if ($job->attempts() > 3) {
            return $job->failed();
        }

        $database = Arr::get($data, 'database');
        $migrator = $this->resolveMigrator($data);
        $entity   = $this->resolveModelEntity($migrator, $data);

        if (is_null($entity)) {
            return $job->release(10);
        }

        $migrator->runReset($entity, $database);

        $job->delete();
    }
}
