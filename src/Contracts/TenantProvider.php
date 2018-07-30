<?php

namespace Orchestra\Tenanti\Contracts;

use Orchestra\Tenanti\Tenantor;

interface TenantProvider
{
    /**
     * Convert to tenantor.
     *
     * @return \Orchestra\Tenanti\Tenantor
     */
    public function asTenantor(): Tenantor;
}
