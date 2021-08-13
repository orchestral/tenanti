<?php

namespace Orchestra\Tenanti\Notice;

use Illuminate\Database\Migrations\Migrator;
use Orchestra\Tenanti\Contracts\Notice;
use Symfony\Component\Console\Output\OutputInterface;

class Command implements Notice
{
    /**
     * The output implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Construct Notice implementation for Command.
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Raise a note event.
     *
     * @param  string  $message
     */
    public function add(...$message): void
    {
        $this->send(...$message);
    }

    /**
     * Merge migrator operation notes.
     */
    public function mergeWith(Migrator $migrator): void
    {
        $migrator->setOutput($this->output);
    }

    /**
     * Send output of notes.
     *
     * @param  string|array  $notes
     */
    protected function send($notes): void
    {
        foreach ((array) $notes as $note) {
            $this->output->writeln($note);
        }
    }
}
