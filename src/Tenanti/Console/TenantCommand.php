<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\Command;
use Orchestra\Tenanti\Migrator;

class TenantCommand extends Command
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

    }
}
