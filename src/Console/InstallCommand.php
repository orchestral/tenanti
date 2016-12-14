<?php

namespace Orchestra\Tenanti\Console;

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
    public function handle()
    {
        $driver   = $this->getDriver();
        $database = $this->option('database');
        $id       = $this->option('id');

        $migrator = $this->tenant->driver($driver);

        $this->setupMigrationOutput($migrator);

        $migrator->install($database, $id);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['id', null, InputOption::VALUE_OPTIONAL, 'The entity ID (for single entity operation).'],
        ];
    }
}
