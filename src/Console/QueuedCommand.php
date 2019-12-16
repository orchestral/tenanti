<?php

namespace Orchestra\Tenanti\Console;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\Kernel;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
     * @return void
     */
    public function handle(Kernel $kernel)
    {
        if (! $this->confirmToProceed()) {
            return 126;
        }

        $driver = $this->tenantDriverName();
        $action = $this->argument('action');
        $database = $this->option('database');
        $queue = $this->option('queue') ?? $this->tenant()->getConfiguration()['queue'] ?? 'default';
        $delay = $this->option('delay');

        if (! \in_array($action, $this->actions)) {
            throw new InvalidArgumentException("Action [{$action}] is not available for this command.");
        }

        $command = "tenanti:{$action}";
        $parameters = ['driver' => $driver, '--database' => $database, '--force' => true];

        $this->tenant()->driver($driver)
            ->executeForEach(static function ($entity) use ($kernel, $command, $parameters, $queue, $delay) {
                $job = $kernel->queue(
                    $command, \array_merge($parameters, ['--id' => $entity->getKey()])
                )->onQueue($queue);

                if ($delay > 0) {
                    $job->delay($delay);
                }
            });

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
            ['queue', null, InputOption::VALUE_OPTIONAL, 'The queue connection to use.'],
            ['delay', null, InputOption::VALUE_OPTIONAL, 'The number of seconds to delay failed jobs.', 0],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
