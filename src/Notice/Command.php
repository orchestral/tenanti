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
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Raise a note event.
     *
     * @param  string  $message
     *
     * @return void
     */
    public function add(...$message): void
    {
        $this->send(...$message);
    }

    /**
     * Merge migrator operation notes.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     *
     * @return void
     */
    public function mergeWith(Migrator $migrator): void
    {
        $migrator->setOutput($this->output);
    }

    /**
     * Send output of notes.
     *
     * @param  array  $notes
     *
     * @return void
     */
    protected function send(array $notes): void
    {
        foreach ($notes as $note) {
            $this->output->writeln($note);
        }
    }
}
