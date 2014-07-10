<?php namespace Orchestra\Tenanti;

class DatabaseMigrationRepository extends \Illuminate\Database\Migrations\DatabaseMigrationRepository
{
    /**
     * Get table name.
     *
     * @param  string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set table name.
     *
     * @param  string   $table
     * @return DatabaseMigrationRepository
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }
}
