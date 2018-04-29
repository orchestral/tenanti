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
     * Merge migrator operation notes.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     *
     * @return void
     */
    protected function mergeMigratorNotes(BaseMigrator $migrator): void
    {
        if ($this->notice instanceof Notice) {
            $this->notice->mergeFrom($migrator);
        }
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
}
