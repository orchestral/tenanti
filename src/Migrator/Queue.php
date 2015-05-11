<?php namespace Orchestra\Tenanti\Migrator;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\App;

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
        $task = App::make('Orchestra\Tenanti\Jobs\CreateTenant');

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
        $task = App::make('Orchestra\Tenanti\Jobs\DeleteTenant');

        return $task->fire($job, $data);
    }
}
