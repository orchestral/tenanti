<?php

namespace Orchestra\Tenanti\Console;

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
            return 126;
        }

        \tap($this->tenant->driver($this->getDriver()), function ($migrator) {
            $this->setupMigrationOutput($migrator);

            $migrator->reset(
                $this->option('database'), $this->option('id'), $this->option('pretend', false)
            );
        });

        return 0;
    }
}
