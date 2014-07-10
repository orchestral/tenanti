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
        $pretend = $this->option('pretend');
        $driver  = $this->argument('driver');

        $this->tenant->driver($driver)->run($pretend);
    }
}
