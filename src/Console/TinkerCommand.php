<?php

namespace Orchestra\Tenanti\Console;

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
        $arguments = $this->getArgumentsWithDriver('id');

        \tap($this->tenant->driver($arguments['driver']), static function ($tenanti) use ($arguments) {
            $tenanti->asDefaultConnection(
                $tenanti->getModel()->findOrFail($arguments['id']), 'tinker'
            );
        });

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
