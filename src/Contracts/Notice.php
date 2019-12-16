<?php

namespace Orchestra\Tenanti\Contracts;

use Illuminate\Database\Migrations\Migrator;

interface Notice
{
    /**
     * Raise a note event.
     *
     * @param  string  $message
     */
    public function add(...$message): void;

    /**
     * Merge migrator operation notes.
     */
    public function mergeWith(Migrator $migrator): void;
}
