<?php

namespace Orchestra\Tenanti\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Orchestra\Tenanti\Contracts\Factory;

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
    protected function shouldBeFailed(): bool
    {
        if ($this->attempts() > 3 && $this->job) {
            $this->fail(null);

            return true;
        }

        return false;
    }

    /**
     * Should the job be delayed.
     *
     * @return bool
     */
    protected function shouldBeDelayed(): bool
    {
        if ($this->job && is_null($this->model)) {
            $this->release(10);

            return true;
        }

        return false;
    }

    /**
     * Resolve migrator instance.
     *
     * @return \Orchestra\Tenanti\Contracts\Factory
     */
    protected function resolveMigrator(): Factory
    {
        return resolve('orchestra.tenanti')->driver($this->config['driver'] ?? null);
    }
}
