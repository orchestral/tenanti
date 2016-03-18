<?php namespace Orchestra\Tenanti\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
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
        $this->model  = $model;
        $this->config = $config;
    }

    /**
     * Resolve migrator instance.
     *
     * @return \Orchestra\Tenanti\Migrator\FactoryInterface
     */
    protected function resolveMigrator()
    {
        $driver = Arr::get($this->config, 'driver');

        return App::make('orchestra.tenanti')->driver($driver);
    }

    /**
     * Fail the job from the queue.
     *
     * @return void
     */
    public function failed()
    {
        if ($this->job) {
            return $this->job->failed();
        }
    }
}
