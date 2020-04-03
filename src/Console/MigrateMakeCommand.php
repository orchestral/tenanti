<?php

namespace Orchestra\Tenanti\Console;

use Illuminate\Support\Composer;
use InvalidArgumentException;
use Orchestra\Tenanti\Migrator\MigrationWriter;
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
     * Execute the console command.
     *
     * @return int
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

        if (\is_bool($create) && empty($table)) {
            throw new InvalidArgumentException('Please set the table name for this migration using --table option!');
        } elseif (! $table && \is_string($create)) {
            $table = $create;
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.

        \with($this->laravel->make(MigrationWriter::class), function ($writer) use ($driver, $name, $table, $create) {
            $file = $writer($driver, $name, $table, $create);

            $this->line("<info>Created Migration:</info> $file");
        });

        $composer->dumpAutoloads();

        return 0;
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
            ['table', null, InputOption::VALUE_REQUIRED, 'The table to migrate.'],
        ];
    }
}
