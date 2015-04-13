<?php namespace Orchestra\Tenanti\Console;

use Orchestra\Support\Str;
use Illuminate\Foundation\Composer;
use Orchestra\Tenanti\TenantiManager;
use Orchestra\Tenanti\Migrator\Creator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
     * @var \Orchestra\Tenanti\Migrator\Creator
     */
    protected $creator;

    /**
     * @var \Illuminate\Foundation\Composer
     */
    protected $composer;

    /**
     * Create a make migration command instance.
     *
     * @param \Orchestra\Tenanti\TenantiManager  $tenant
     * @param \Orchestra\Tenanti\Migrator\Creator  $creator
     * @param \Illuminate\Foundation\Composer  $composer
     */
    public function __construct(TenantiManager $tenant, Creator $creator, Composer $composer)
    {
        $this->creator  = $creator;
        $this->composer = $composer;

        parent::__construct($tenant);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $driver = $this->input->getArgument('driver');
        $name   = $this->input->getArgument('name');
        $create = $this->input->getOption('create');
        $table  = $this->input->getOption('table');

        if (! $table && is_string($create)) {
            $table = $create;
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($driver, $name, $table, $create);

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $driver
     * @param  string  $name
     * @param  string  $table
     * @param  bool    $create
     *
     * @return string
     */
    protected function writeMigration($driver, $name, $table, $create)
    {
        $migrator = $this->tenant->driver($driver);
        $files    = $this->creator->getFilesystem();
        $path     = $migrator->getMigrationPath();

        if (! $files->isDirectory($path)) {
            $files->makeDirectory($path, 0755, true);
        }

        $name  = implode('_', [$driver, 'tenant', $name]);
        $table = Str::replace($migrator->getTablePrefix()."_{$table}", ['id' => '{$id}']);

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
        return [
            ['driver', InputArgument::REQUIRED, 'Tenant driver name.'],
            ['name', InputArgument::REQUIRED, 'The name of the migration'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['create', null, InputOption::VALUE_OPTIONAL, 'The table to be created.'],
            ['table', null, InputOption::VALUE_OPTIONAL, 'The table to migrate.'],
        ];
    }
}
