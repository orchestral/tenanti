<?php

namespace Orchestra\Tenanti\Console;

use Illuminate\Support\Composer;
use Orchestra\Support\Str;
use Orchestra\Tenanti\Migrator\Creator;
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
     * @var \Orchestra\Tenanti\Migrator\Creator
     */
    protected $creator;

    /**
     * Create a make migration command instance.
     */
    public function __construct(Creator $creator)
    {
        $this->creator = $creator;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Composer $composer)
    {
        $arguments = $this->getArgumentsWithDriver('name');
        $driver = $arguments['driver'];
        $name = $arguments['name'];

        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $create = $this->input->getOption('create') ?? false;
        $table = $this->input->getOption('table');

        if (! $table && \is_string($create)) {
            $table = $create;
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($driver, $name, $table, $create);

        $composer->dumpAutoloads();
    }

    /**
     * Write the migration file to disk.
     */
    protected function writeMigration(
        ?string $driver,
        string $name,
        ?string $table,
        bool $create = false
    ): string {
        $migrator = $this->tenantDriver($driver);
        $files = $this->creator->getFilesystem();
        $path = $migrator->getMigrationPaths()[0] ?? null;

        if (! $files->isDirectory($path)) {
            $files->makeDirectory($path, 0755, true);
        }

        if ($this->tenant()->config("{$driver}.shared", true) === true) {
            $table = Str::replace($migrator->getTablePrefix()."_{$table}", ['id' => '{$id}']);
        }

        $name = \implode('_', [$driver, 'tenant', $name]);

        $file = \pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);

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
            ['driver', InputArgument::OPTIONAL, 'Tenant driver name.'],
            ['name', InputArgument::OPTIONAL, 'The name of the migration'],
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
            ['create', false, InputOption::VALUE_OPTIONAL, 'The table to be created.'],
            ['table', null, InputOption::VALUE_OPTIONAL, 'The table to migrate.'],
        ];
    }
}
