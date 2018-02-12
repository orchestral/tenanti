<?php

namespace Orchestra\Tenanti;

use Illuminate\Database\Migrations\Migration as BaseMigration;

class Migration extends BaseMigration
{
    /**
     * Set connection for the migration.
     *
     * @param  string|null  $name
     *
     * @return $this
     */
    public function setConnection(string $name): self
    {
        $this->connection = $name;

        return $this;
    }
}
