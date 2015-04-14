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
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $driver   = $this->argument('driver');
        $database = $this->option('database');
        $id       = $this->option('id');
        $pretend  = $this->option('pretend');

        $this->prepareDatabase($driver, $database, $id);

        $migrator = $this->tenant->driver($driver);

        $migrator->run($database, $id, $pretend);

        $this->writeMigrationOutput($migrator);
    }

    /**
     * Prepare the migration database for running.
     *
     * @param  string  $driver
     * @param  string|null  $database
     * @param  mixed|null  $id
     *
     * @return void
     */
    protected function prepareDatabase($driver, $database, $id = null)
    {
        $parameters = [
            'driver'     => $driver,
            '--database' => $database,
        ];

        if (! is_null($id)) {
            $parameters['--id'] = $id;
        }

        $this->call('tenanti:install', $parameters);
    }
}
