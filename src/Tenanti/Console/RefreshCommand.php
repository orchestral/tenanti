<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\ConfirmableTrait;

class RefreshCommand extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and re-run all migrations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ( ! $this->confirmToProceed()) {
            return;
        }

        $driver   = $this->argument('driver');
        $database = $this->option('database');
        $force    = $this->option('force');

        $this->call("tenanti:reset", array(
            $driver,
            '--database' => $database,
            '--force' => $force,
        ));

        // The refresh command is essentially just a brief aggregate of a few other of
        // the migration commands and just provides a convenient wrapper to execute
        // them in succession. We'll also see if we need to re-seed the database.
        $this->call("tenanti:migrate", array(
            $driver,
            '--database' => $database,
            '--force' => $force,
        ));
    }
}
