<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseCommand extends Command
{
    /**
     * Tenant manager instance.
     *
     * @var \Orchestra\Tenanti\TenantiManager
     */
    protected $tenant;

    /**
     * Create a new migration command instance.
     *
     * @param  \Orchestra\Tenanti\TenantiManager $tenant
     */
    public function __construct(TenantiManager $tenant)
    {
        $this->tenant = $tenant;

        parent::__construct();
    }
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('name', InputArgument::REQUIRED, 'Tenant Driver Name.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'),
        );
    }
}
