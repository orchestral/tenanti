<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\ConfirmableTrait;

class RollbackCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last database migration';

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

        $migrator = $this->tenant->driver($driver);

        $migrator->rollback($database, $pretend);
    }
}
