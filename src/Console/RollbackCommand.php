<?php

namespace Orchestra\Tenanti\Console;

use Illuminate\Console\ConfirmableTrait;

class RollbackCommand extends BaseCommand
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
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $driver   = $this->getDriver();
        $database = $this->option('database');
        $id       = $this->option('id');
        $pretend  = $this->option('pretend', false);

        $migrator = $this->tenant->driver($driver);

        $this->setupMigrationOutput($migrator);

        $migrator->rollback($database, $id, $pretend);
    }
}
