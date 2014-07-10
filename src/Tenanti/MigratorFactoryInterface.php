<?php namespace Orchestra\Tenanti;

use Illuminate\Database\Eloquent\Model;

interface MigratorFactoryInterface
{
    /**
     * Install migrations.
     *
     * @param  string|null  $database
     * @return void
     */
    public function install($database);

    /**
     * Run migrations.
     *
     * @param  bool $pretend
     * @return void
     */
    public function run($pretend = false);

    /**
     * Rollback migrations.
     *
     * @return void
     */
    public function rollback($pretend = false);

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null                          $database
     * @return void
     */
    public function runInstall(Model $entity, $database);

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  bool                                 $pretend
     * @return void
     */
    public function runUp(Model $entity, $pretend = false);

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  bool                                 $pretend
     * @return void
     */
    public function runDown(Model $entity, $pretend = false);

    /**
     * Get migration path.
     *
     * @return mixed
     */
    public function getMigrationPath();

    /**
     * Get model name.
     *
     * @return mixed
     */
    public function getModelName();

    /**
     * Get table prefix.
     *
     * @return string
     */
    public function getTablePrefix();
}