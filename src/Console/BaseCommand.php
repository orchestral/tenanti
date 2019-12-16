<?php

namespace Orchestra\Tenanti\Console;

use Illuminate\Console\Command;
use Orchestra\Tenanti\Contracts\Factory;
use Orchestra\Tenanti\Notice\Command as Notice;
use Orchestra\Tenanti\TenantiManager;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseCommand extends Command
{
    /**
     * Tenant manager instance.
     *
     * @var \Orchestra\Tenanti\TenantiManager
     */
    protected $tenant;

    /**
     * Write migration output.
     */
    protected function setupMigrationOutput(Factory $migrator): void
    {
        $migrator->setNotice(new Notice($this->output));
    }

    /**
     * Get driver argument or first driver in the config.
     */
    protected function getDriver(TenantiManager $tenant): string
    {
        $driver = $this->argument('driver') ?: $this->getDriverFromConfig($tenant);

        if (! empty($driver)) {
            return $driver;
        }

        throw new RuntimeException('Not enough arguments (missing: "driver").');
    }

    /**
     * Get first driver in the config.
     *
     * @return string
     */
    protected function getDriverFromConfig(TenantiManager $tenant): ?string
    {
        $drivers = \array_keys($tenant->config('drivers'));

        if (\count($drivers) === 1) {
            return $drivers[0];
        }

        return null;
    }

    /**
     * Get the required arguments when used with optional driver argument.
     */
    protected function getArgumentsWithDriver(...$arguments): array
    {
        \array_unshift($arguments, 'driver');

        $resolvedArguments = [];

        $this->validateMissingArguments($arguments);

        if (empty($this->argument(\end($arguments)))) {
            $driver = $this->getDriverFromConfig($this->tenant());

            if (empty($driver)) {
                throw new RuntimeException('Not enough arguments (missing: "driver").');
            }

            $resolvedArguments['driver'] = $driver;

            for ($i = 1; $i < \count($arguments); $i++) {
                $resolvedArguments[$arguments[$i]] = $this->argument($arguments[$i - 1]);
            }
        } else {
            foreach ($arguments as $argument) {
                $resolvedArguments[$argument] = $this->argument($argument);
            }
        }

        return $resolvedArguments;
    }

    /**
     * Validate missing arguments.
     *
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    protected function validateMissingArguments(array $arguments): bool
    {
        $missingArguments = \array_filter($arguments, function ($argument) {
            return empty($this->argument($argument));
        });

        if (\count($missingArguments) > 1) {
            throw new RuntimeException(\sprintf('Not enough arguments (missing: "%s").', \implode(', ', $missingArguments)));
        }

        return true;
    }

    /**
     * Get tenant manager instance.
     */
    protected function tenant(): TenantiManager
    {
        if (! isset($this->tenant)) {
            $this->tenant = $this->laravel->make('orchestra.tenanti');
        }

        return $this->tenant;
    }

    /**
     * Get tenant driver.
     */
    protected function tenantDriver(?string $name = null): Factory
    {
        return $this->tenant()->driver($name ?: $this->tenantDriverName());
    }

    /**
     * Get tenant driver name.
     */
    protected function tenantDriverName(): string
    {
        return $this->getDriver($this->tenant());
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
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['id', null, InputOption::VALUE_OPTIONAL, 'The entity ID (for single entity operation).'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
