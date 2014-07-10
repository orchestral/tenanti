<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Database\Migrations\MigrationCreator;
use Orchestra\Tenanti\TenantiManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateMakeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * Create a make migration command instance.
     *
     * @param  \Orchestra\Tenanti\TenantiManager                $tenant
     * @param  \Illuminate\Database\Migrations\MigrationCreator $creator
     */
    public function __construct(TenantiManager $tenant, MigrationCreator $creator)
    {
        $this->creator = $creator;

        parent::__construct($tenant);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $driver = $this->input->getArgument('driver');
        $name   = $this->input->getArgument('name');
        $table  = $this->input->getOption('table');
        $create = $this->input->getOption('create');

        if ( ! $table && is_string($create)) {
            $table = $create;
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($driver, $name, $table, $create);

        $this->call('dump-autoload');
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $driver
     * @param  string  $name
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function writeMigration($driver, $name, $table, $create)
    {
        $path = $this->tenant->driver($driver)->getMigrationPath();

        $file = pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> $file");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('driver', InputArgument::REQUIRED, 'Tenant driver name.'),
            array('name', InputArgument::REQUIRED, 'The name of the migration'),
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
            array('create', null, InputOption::VALUE_OPTIONAL, 'The table to be created.'),
            array('table', null, InputOption::VALUE_OPTIONAL, 'The table to migrate.'),
        );
    }
}
