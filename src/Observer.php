<?php

namespace Orchestra\Tenanti;

use Illuminate\Database\Eloquent\Model;
use Orchestra\Tenanti\Jobs\CreateTenant;
use Orchestra\Tenanti\Jobs\DeleteTenant;
use Illuminate\Foundation\Bus\DispatchesJobs;

abstract class Observer
{
    use DispatchesJobs;

    /**
     * Get connection name.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        return;
    }

    /**
     * Get driver name.
     *
     * @return string
     */
    abstract public function getDriverName();

    /**
     * Run on created observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return bool
     */
    public function created(Model $entity)
    {
        $data = [
            'database' => $this->getConnectionName(),
            'driver' => $this->getDriverName(),
        ];

        $this->dispatch($this->getCreateTenantJob($entity, $data));

        return true;
    }

    /**
     * Run on restored observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return bool
     */
    public function restored(Model $entity)
    {
        $data = [
            'database' => $this->getConnectionName(),
            'driver' => $this->getDriverName(),
        ];

        $this->dispatch($this->getRestoreTenantJob($entity, $data));

        return true;
    }

    /**
     * Run on deleted observer.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     *
     * @return bool
     */
    public function deleted(Model $entity)
    {
        $data = [
            'database' => $this->getConnectionName(),
            'driver' => $this->getDriverName(),
        ];

        $this->dispatch($this->getDeleteTenantJob($entity, $data));

        return true;
    }

    /**
     * Resolve create tenant job.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  array  $data
     *
     * @return \Orchestra\Tenanti\Jobs\CreateTenant
     */
    protected function getCreateTenantJob(Model $entity, array $data)
    {
        return new CreateTenant($entity, $data);
    }

    /**
     * Resolve restore tenant job.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  array  $data
     *
     * @return \Orchestra\Tenanti\Jobs\CreateTenant
     */
    protected function getRestoreTenantJob(Model $entity, array $data)
    {
        return new CreateTenant($entity, $data);
    }

    /**
     * Resolve create tenant job.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  array  $data
     *
     * @return \Orchestra\Tenanti\Jobs\DeleteTenant
     */
    protected function getDeleteTenantJob(Model $entity, array $data)
    {
        return new DeleteTenant($entity, $data);
    }
}
