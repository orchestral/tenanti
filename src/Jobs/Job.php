<?php

namespace Orchestra\Tenanti\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Orchestra\Tenanti\Contracts\Factory as FactoryContract;

/**
 * @property \Illuminate\Contracts\Queue\Job|null  $job
 */
abstract class Job
{
    use InteractsWithQueue, Queueable;

    /**
     * The eloquent model.
     *
     * @var \Illuminate\Database\Eloquent\Model|null
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
     * @param \Illuminate\Database\Eloquent\Model|null  $model
     */
    public function __construct($model, array $config)
    {
        $this->model = $model;
        $this->config = $config;
    }

    /**
     * Should the job be failed.
     */
    protected function shouldBeFailed(): bool
    {
        if ($this->job && $this->attempts() > 3) {
            $this->fail(null);

            return true;
        }

        return false;
    }

    /**
     * Should the job be delayed.
     */
    protected function shouldBeDelayed(): bool
    {
        if ($this->job && \is_null($this->model)) {
            $this->release(10);

            return true;
        }

        return false;
    }

    /**
     * Resolve migrator instance.
     */
    protected function resolveMigrator(): FactoryContract
    {
        return \resolve('orchestra.tenanti')->driver($this->config['driver'] ?? null);
    }
}
