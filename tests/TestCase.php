<?php

namespace Orchestra\Tenanti\Tests;

use Orchestra\Testbench\TestCase as Testbench;

abstract class TestCase extends Testbench
{
    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Tenanti' => 'Orchestra\Support\Facades\Tenanti',
        ];
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Orchestra\Tenanti\TenantiServiceProvider',
            'Orchestra\Tenanti\CommandServiceProvider',
        ];
    }
}
