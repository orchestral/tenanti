<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\ConfirmableTrait;

class ResetCommand extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all database migrations';

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

        $migrator = $this->tenant->driver($driver);

        $migrator->reset($database, $id, $pretend);

        $this->writeMigrationOutput($migrator);
    }
}
