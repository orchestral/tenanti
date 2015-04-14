<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\Command;
use Orchestra\Tenanti\TenantiManager;
use Orchestra\Tenanti\Migrator\FactoryInterface;
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
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['driver', InputArgument::REQUIRED, 'Tenant driver name.'],
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
