<?php namespace Orchestra\Tenanti\Migrator;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\App;
use Orchestra\Tenanti\Jobs\CreateTenant;
use Orchestra\Tenanti\Jobs\DeleteTenant;

class Queue
{
    /**
     * Run queue on creating a model.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  array  $data
     *
     * @return void
     */
    public function create(Job $job, array $data)
    {
        $task = App::make(CreateTenant::class);

        return $task->fire($job, $data);
    }

    /**
     * Run queue on deleting a model.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  array  $data
     *
     * @return void
     */
    public function delete(Job $job, array $data)
    {
        $task = App::make(DeleteTenant::class);

        return $task->fire($job, $data);
    }
}
