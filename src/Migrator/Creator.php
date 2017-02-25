<?php

namespace Orchestra\Tenanti\Migrator;

use Illuminate\Database\Migrations\MigrationCreator;

class Creator extends MigrationCreator
{
    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }
}
