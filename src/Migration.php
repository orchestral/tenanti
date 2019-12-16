<?php

namespace Orchestra\Tenanti;

use Illuminate\Database\Migrations\Migration as BaseMigration;

class Migration extends BaseMigration
{
    /**
     * Set connection for the migration.
     *
     * @return $this
     */
    public function setConnection(?string $name)
    {
        $this->connection = $name;

        return $this;
    }
}
