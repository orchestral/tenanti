<?php

namespace Orchestra\Tenanti\Migrator;

use Orchestra\Tenanti\Contracts\Notice;
use Illuminate\Database\Migrations\Migrator as BaseMigrator;

trait Notable
{
    /**
     * The notice implementation.
     *
     * @var \Orchestra\Tenanti\Contracts\Notice|null
     */
    protected $notice;

    /**
     * Set notice implementation.
     *
     * @param  \Orchestra\Tenanti\Contracts\Notice  $notice
     *
     * @return $this
     */
    public function setNotice(Notice $notice)
    {
        $this->notice = $notice;

        return $this;
    }

    /**
     * Raise a note event.
     *
     * @param  string  $message
     *
     * @return void
     */
    protected function note(...$message): void
    {
        if ($this->notice instanceof Notice) {
            $this->notice->add($message);
        }
    }

    /**
     * Resolve migrator with notable.
     *
     * @param  string  $table
     * @return \Orchestra\Tenanti\Migrator\Migrator
     */
    protected function resolveMigratorWithNotes(string $table): Migrator
    {
        $migrator = $this->resolveMigrator($table);

        if ($this->notice instanceof Notice) {
            $this->notice->mergeWith($migrator);
        }

        return $migrator;
    }

    /**
     * Resolve migrator.
     *
     * @param  string  $table
     *
     * @return \Orchestra\Tenanti\Migrator\Migrator
     */
    abstract protected function resolveMigrator(string $table): Migrator;
}
