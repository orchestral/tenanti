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

        \tap($this->tenantDriver(), function ($migrator) {
            $this->setupMigrationOutput($migrator);

            $migrator->rollback(
                $this->option('database'), $this->option('id'), $this->option('pretend', false)
            );
        });
    }
}
