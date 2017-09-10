<?php

namespace Orchestra\Tenanti\Console;

use InvalidArgumentException;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class QueuedCommand extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running tenanti action command using queue.';

    /**
     * List of valid actions.
     *
     * @var array
     */
    protected $actions = ['install', 'migrate', 'rollback', 'reset', 'refresh'];

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Contracts\Console\Kernel  $kernel
     *
     * @return void
     */
    public function handle(Kernel $kernel)
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $driver   = $this->getDriver();
        $action   = $this->argument('action');
        $database = $this->option('database');
        $queue    = $this->option('queue');

        if (! in_array($action, $this->actions)) {
            throw new InvalidArgumentException("Action [{$action}] is not available for this command.");
        }

        $command    = "tenanti:{$action}";
        $parameters = ['driver' => $driver, '--database' => $database, '--force' => true];

        $migrator = $this->tenant->driver($driver);

        $migrator->executeByChunk(function ($entities) use ($kernel, $command, $parameters) {
            foreach ($entities as $entity) {
                $kernel->queue(
                    $command, array_merge($parameters, ['--id' => $entity->getKey()])
                )->onQueue($this->option('queue'));
            }
        });
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
            ['action', InputArgument::REQUIRED, 'Tenant action name (install|migrate|rollback|reset|refresh).'],
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
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['queue', null, InputOption::VALUE_OPTIONAL, 'The queue connection to use.', 'default'],
            // @deprecated
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
