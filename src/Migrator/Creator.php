<?php

namespace Orchestra\Tenanti\Migrator;

use Illuminate\Database\Migrations\MigrationCreator;

class Creator extends MigrationCreator
{
    /**
     * Get the path to the stubs.
     */
    public function stubPath(): string
    {
        return __DIR__.'/stubs';
    }
}
