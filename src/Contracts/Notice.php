<?php

namespace Orchestra\Tenanti\Contracts;

use Illuminate\Database\Migrations\Migrator;

interface Notice
{
    /**
     * Raise a note event.
     *
     * @param  string  $message
     *
     * @return void
     */
    public function add(...$message);

    /**
     * Merge migrator operation notes.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     *
     * @return void
     */
    public function mergeFrom(Migrator $migrator);
}
