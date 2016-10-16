<?php namespace Orchestra\Tenanti\Console;

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
        $driver  = $this->argument('driver');
        $id      = $this->argument('id');
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
            ['driver', InputArgument::REQUIRED, 'Tenant driver name.'],
            ['id', InputArgument::REQUIRED, 'The entity ID.'],
        ];
    }
}
