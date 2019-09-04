<?php

namespace Orchestra\Tenanti\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Factory
{
    /**
     * Install migrations.
     *
     * @param  string|null  $database
     *
     * @return void
     */
    public function install(?string $database): void;

    /**
     * Run migrations.
     *
     * @param  string|null  $database
     * @param  mixed|null  $id
     * @param  bool  $pretend
     *
     * @return void
     */
    public function run(?string $database, $id = null, bool $pretend = false): void;

    /**
     * Rollback migrations.
     *
     * @param  string|null  $database
     * @param  mixed|null  $id
     * @param  bool  $pretend
     *
     * @return void
     */
    public function rollback(?string $database, $id = null, bool $pretend = false): void;

    /**
     * Reset migrations.
     *
     * @param  string|null  $database
     * @param  mixed|null  $id
     * @param  bool  $pretend
     *
     * @return void
     */
    public function reset(?string $database, $id = null, bool $pretend = false): void;

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     *
     * @return void
     */
    public function runInstall(Model $entity, ?string $database): void;

    /**
     * Run migration up on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runUp(Model $entity, ?string $database, bool $pretend = false): void;

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runDown(Model $entity, ?string $database, bool $pretend = false): void;

    /**
     * Run migration down on a single entity.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $entity
     * @param  string|null  $database
     * @param  bool  $pretend
     *
     * @return void
     */
    public function runReset(Model $entity, ?string $database, bool $pretend = false): void;

    /**
     * Get migration paths.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $entity
     *
     * @return array|null
     */
    public function getMigrationPaths(Model $entity = null): ?array;

    /**
     * Get model name.
     *
     * @return string
     */
    public function getModelName(): string;

    /**
     * Get table prefix.
     *
     * @return string
     */
    public function getTablePrefix(): string;
}
