<?php namespace Orchestra\Tenanti\Console;

use Symfony\Component\Console\Input\InputOption;

class InstallCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the migration repository';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $driver   = $this->argument('driver');
        $database = $this->option('database');

        $migrator = $this->tenant->driver($driver);

        $migrator->install($database);

        $this->writeMigrationOutput($migrator);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'),
        );
    }
}
