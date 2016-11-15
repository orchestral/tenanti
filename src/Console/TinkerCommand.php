<?php

namespace Orchestra\Tenanti\Console;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;

class TinkerCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tenanti:tinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tinker using tenant connection';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $arg1 = $this->argument('driver');
        $arg2 = $this->argument('id');

        if (empty($arg1) && empty($arg2)) {
            throw new RuntimeException('Not enough arguments (missing: "driver, id").');
        } else if (empty($arg2)) {
            $id = $arg1;
            $driver = $this->getDriverFromConfig();

            if (empty($driver)) {
                throw new RuntimeException('Not enough arguments (missing: "driver").');
            }
        } else {
            $id = $arg2;
            $driver = $arg1;
        }

        $tenanti = $this->tenant->driver($driver);

        $model   = $tenanti->getModel()->findOrFail($id);
        $tenanti->asDefaultConnection($model, 'tinker');

        $this->call('tinker');
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
            ['id', InputArgument::OPTIONAL, 'The entity ID.'],
        ];
    }
}
