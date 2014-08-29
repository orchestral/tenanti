<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\ConfirmableTrait;

class MigrateCommand extends BaseCommand
{
    use ConfirmableTrait;

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
    protected $description = 'Run the database migrations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $driver   = $this->argument('driver');
        $database = $this->option('database');
        $pretend  = $this->option('pretend');

        $this->prepareDatabase($driver, $database);

        $migrator = $this->tenant->driver($driver);

        $migrator->run($database, $pretend);

        $this->writeMigrationOutput($migrator);
    }

    /**
     * Prepare the migration database for running.
     *
     * @param  string       $driver
     * @param  string|null  $database
     * @return void
     */
    protected function prepareDatabase($driver, $database)
    {
        $this->call("tenanti:install", array(
            'driver'     => $driver,
            '--database' => $database,
        ));
    }
}
