<?php

namespace Orchestra\Tenanti\Contracts;

use Orchestra\Tenanti\Tenantor;

interface TenantProvider
{
    /**
     * Convert to tenantor.
     */
    public function asTenantor(): Tenantor;
}
