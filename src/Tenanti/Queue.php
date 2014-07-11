<?php namespace Orchestra\Tenanti;

use Illuminate\Queue\Jobs\Job;
use Orchestra\Tenanti\Migrator\FactoryInterface;

class Queue
{
    protected $tenant;

    public function __construct(TenantiManager $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Run queue on creating a model.
     *
     * @param  \Illuminate\Queue\Jobs\Job   $job
     * @param  array                        $data
     * @return void
     */
    public function create(Job $job, array $data)
    {
        $database = array_get($data, 'database');
        $migrator = $this->resolveMigrator($data);
        $entity   = $this->resolveModelEntity($migrator, $data);

        if (is_null($entity)) {
            $job->delete();
            return ;
        }

        $migrator->runUp($entity, $database);
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
        $database = array_get($data, 'database');
        $migrator = $this->resolveMigrator($data);
        $entity   = $this->resolveModelEntity($migrator, $data);

        if (is_null($entity)) {
            $job->delete();
            return ;
        }

        $migrator->runReset($entity, $database);
    }

    /**
     * Resolve migrator instance.
     *
     * @param  array    $data
     * @return \Orchestra\Tenanti\Migrator\FactoryInterface
     */
    protected function resolveMigrator(array $data)
    {
        $driver = array_get($data, 'driver');

        return $this->tenant->driver($driver);
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
        $id = array_get($data, 'id');

        return $migrator->getModel()->newInstance()->find($id);
    }
}
