<?php namespace Orchestra\Tenanti\Migrator;

use Illuminate\Support\Arr;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\App;

class Queue
{
    /**
     * Run queue on creating a model.
     *
     * @param  \Illuminate\Queue\Jobs\Job   $job
     * @param  array                        $data
     * @return void
     */
    public function create(Job $job, array $data)
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

    /**
     * Run queue on deleting a model.
     *
     * @param  \Illuminate\Queue\Jobs\Job   $job
     * @param  array                        $data
     * @return void
     */
    public function delete(Job $job, array $data)
    {
        $database = Arr::get($data, 'database');
        $migrator = $this->resolveMigrator($data);
        $entity   = $this->resolveModelEntity($migrator, $data);

        if (is_null($entity)) {
            $job->delete();
            return ;
        }

        $migrator->runReset($entity, $database);

        $job->delete();
    }

    /**
     * Resolve migrator instance.
     *
     * @param  array    $data
     * @return \Orchestra\Tenanti\Migrator\FactoryInterface
     */
    protected function resolveMigrator(array $data)
    {
        $driver = Arr::get($data, 'driver');

        return App::make('orchestra.tenanti')->driver($driver);
    }

    /**
     * Resolve model entity.
     *
     * @param  \Orchestra\Tenanti\Migrator\FactoryInterface $migrator
     * @param  array                                        $data
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function resolveModelEntity(FactoryInterface $migrator, $data)
    {
        $id = Arr::get($data, 'id');

        return $migrator->getModel()->newInstance()->find($id);
    }
}
