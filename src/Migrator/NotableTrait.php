<?php namespace Orchestra\Tenanti\Migrator;

use Illuminate\Database\Migrations\Migrator as BaseMigrator;

trait NotableTrait
{
    /**
     * The notes for the current operation.
     *
     * @var array
     */
    protected $notes = [];

    /**
     * Merge migrator operation notes.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     *
     * @return void
     */
    protected function mergeMigratorNotes(BaseMigrator $migrator)
    {
        $this->notes = array_merge($this->notes, $migrator->getNotes());
    }

    /**
     * Raise a note event.
     *
     * @param  string  $message
     *
     * @return void
     */
    protected function note($message)
    {
        $this->notes[] = $message;
    }

    /**
     * Get the notes for the last operation.
     *
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Flush notes.
     *
     * @return void
     */
    public function flushNotes()
    {
        $this->notes = [];
    }
}
