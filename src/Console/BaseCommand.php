<?php

namespace Orchestra\Tenanti\Console;

use Illuminate\Console\Command;
use Orchestra\Tenanti\TenantiManager;
use Orchestra\Tenanti\Migrator\FactoryInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

abstract class BaseCommand extends Command
{
    /**
     * Tenant manager instance.
     *
     * @var \Orchestra\Tenanti\TenantiManager
     */
    protected $tenant;

    /**
     * Create a new migration command instance.
     *
     * @param  \Orchestra\Tenanti\TenantiManager  $tenant
     */
    public function __construct(TenantiManager $tenant)
    {
        $this->tenant = $tenant;

        parent::__construct();
    }

    /**
     * Write migration output.
     *
     * @param  \Orchestra\Tenanti\Migrator\FactoryInterface  $migrator
     *
     * @return void
     */
    protected function writeMigrationOutput(FactoryInterface $migrator)
    {
        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }

        $migrator->flushNotes();
    }

    /**
     * Get driver argument or first driver in the config.
     *
     * @return string
     */
    protected function getDriver()
    {
        $argument = $this->argument('driver');

        if (!empty($argument)) {
            return $argument;
        }

        $driver = $this->getDriverFromConfig();

        if (!empty($driver)) {
            return $driver;
        }

        throw new RuntimeException('Not enough arguments (missing: "driver").');
    }

    /**
     * Get first driver in the config.
     *
     * @return string
     */
    protected function getDriverFromConfig()
    {
        $drivers = array_keys($this->tenant->getConfig('drivers'));

        if (count($drivers) === 1) {
            return $drivers[0];
        }

        return null;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['driver', InputArgument::OPTIONAL, 'Tenant driver name.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['id', null, InputOption::VALUE_OPTIONAL, 'The entity ID (for single entity operation).'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
