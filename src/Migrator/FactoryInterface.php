<?php namespace Orchestra\Tenanti\Migrator;

use Illuminate\Database\Eloquent\Model;

interface FactoryInterface
{
    /**
     * Install migrations.
     *
     * @param  string|null  $database
     *
     * @return void
     */
    public function install($database);

    /**
     * Run migrations.
     *
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function run($database, $pretend = false);

    /**
     * Rollback migrations.
     *
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function rollback($database, $pretend = false);

    /**
     * Reset migrations.
     *
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function reset($database, $pretend = false);

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     *
     * @return void
     */
    public function runInstall(Model $entity, $database);

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runUp(Model $entity, $database, $pretend = false);

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runDown(Model $entity, $database, $pretend = false);

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runReset(Model $entity, $database, $pretend = false);

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
     * Get the notes for the last operation.
     *
     * @return array
     */
    public function getNotes();

    /**
     * Get table prefix.
     *
     * @return string
     */
    public function getTablePrefix();

    /**
     * Flush notes.
     *
     * @return void
     */
    public function flushNotes();
}
