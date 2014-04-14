<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Orchestra\Tenanti\Migrator;

class MigrateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate database for tenant.';

    /**
     * Migrator instance.
     *
     * @var \Orchestra\Tenanti\Migrator
     */
    protected $migrator;

    /**
     * Create a new tenant database migration command instance.
     *
     * @param  \Orchestra\Tenanti\Migrator $migrator
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $name = $this->argument('name');

        $outputs = $this->migrator->run($name);

        foreach ($outputs as $output) {

        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Migration Name'],
        ];
    }
}
