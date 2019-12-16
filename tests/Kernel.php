<?php

namespace Orchestra\Tenanti\Tests;

use Exception;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The bootstrap classes for the application.
     *
     * @return void
     */
    protected $bootstrappers = [];

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Report the exception to the exception handler.
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function reportException(Exception $e)
    {
        throw $e;
    }

    /**
     * Get artisan.
     *
     * @return \Illuminate\Contracts\Console\Application
     */
    public function getArtisan()
    {
        return $this->app['artisan'];
    }
}
