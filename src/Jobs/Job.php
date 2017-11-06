<?php

namespace Orchestra\Tenanti\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;

abstract class Job
{
    use InteractsWithQueue, Queueable;

    /**
     * The eloquent model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Tenant configuration.
     *
     * @var array
     */
    public $config = [];

    /**
     * Construct a new Job.
     *
     * @param \Illuminate\Database\Eloquent\Model  $model
     * @param array  $config
     */
    public function __construct(Model $model, array $config)
    {
        $this->model = $model;
        $this->config = $config;
    }

    /**
     * Should the job be failed.
     *
     * @return bool
     */
    protected function shouldBeFailed()
    {
        if ($this->attempts() > 3 && $this->job) {
            $this->job->failed();

            return true;
        }

        return false;
    }

    /**
     * Resolve migrator instance.
     *
     * @return \Orchestra\Tenanti\Contracts\Factory
     */
    protected function resolveMigrator()
    {
        return resolve('orchestra.tenanti')->driver($this->config['driver'] ?? null);
    }
}
