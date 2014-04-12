<?php namespace Orchestra\Tenanti\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the tenant database table';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new session table command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $table = $this->argument('table');
        $name  = "create_tenant_migration_to_{$table}_table";

        $fullPath = $this->createBaseMigration($table);

        $stub = $this->files->get(__DIR__.'/stubs/migration.stub');
        $replacement = [
            'class' => Str::studly($name),
            'table' => $table,
            'field' => $this->option('field'),
        ];

        $stub = Str::replace($stub, $replacement, '{{', '}}');

        $this->files->put($fullPath, $stub);

        $this->info('Migration created successfully!');
    }

    /**
     * Create a base migration file for the table.
     *
     * @param  string  $name
     * @return string
     */
    protected function createBaseMigration($name)
    {
        $path = $this->laravel['path'].'/database/migrations';

        return $this->laravel['migration.creator']->create($name, $path);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['table', InputArgument::REQUIRED, 'Table Name'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('field', null, InputOption::VALUE_OPTIONAL, 'Field name', 'schema_version'),
        );
    }
}
