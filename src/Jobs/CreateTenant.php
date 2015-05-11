<?php namespace Orchestra\Tenanti\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Queue\Job;

class CreateTenant extends Tenant
{
    /**
     * Run queue on creating a model.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  array  $data
     *
     * @return void
     */
    public function fire(Job $job, array $data)
    {
        $database = Arr::get($data, 'database');
        $migrator = $this->resolveMigrator($data);
        $entity   = $this->resolveModelEntity($migrator, $data);

        if (is_null($entity)) {
            $job->delete();
            return ;
        }

        $migrator->runInstall($entity, $database);
        $migrator->runUp($entity, $database);

        $job->delete();
    }
}
