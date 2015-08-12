<?php namespace Orchestra\Tenanti\Jobs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Orchestra\Tenanti\Migrator\FactoryInterface;

abstract class Tenant
{
    /**
     * Resolve migrator instance.
     *
     * @param  array  $data
     *
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
     * @param  \Orchestra\Tenanti\Migrator\FactoryInterface  $migrator
     * @param  array  $data
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function resolveModelEntity(FactoryInterface $migrator, $data)
    {
        $id = Arr::get($data, 'id');

        return $migrator->getModel()->find($id);
    }
}
