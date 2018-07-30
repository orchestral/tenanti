<?php

namespace Orchestra\Tenanti\Eloquent;

use Orchestra\Tenanti\Tenantor;
use Orchestra\Tenanti\Contracts\TenantProvider;

trait Tenantee
{
    /**
     * The tenantor associated with the model.
     *
     * @var \Orchestra\Tenanti\Tenantor
     */
    protected $tenantor;

    /**
     * Construct a new tenant.
     *
     * @param  \Orchestra\Tenanti\Tenantor|\Orchestra\Tenanti\Contracts\TenantProvider  $tenantor
     *
     * @return static
     */
    public static function tenant($tenantor)
    {
        if ($tenantor instanceof TenantProvider) {
            $tenantor = $tenantor->asTenantor();
        }

        return (new static())->setTenantor($tenantor);
    }

    /**
     * Get the tenantor associated with the model.
     *
     * @return \Orchestra\Tenanti\Tenantor|null
     */
    public function getTenantor()
    {
        return $this->tenantor;
    }

    /**
     * Get the tenantor associated with the model.
     *
     * @param  \Orchestra\Tenanti\Tenantor|null  $tenantor
     *
     * @return $this
     */
    public function setTenantor(?Tenantor $tenantor)
    {
        $this->tenantor = $tenantor;
        $this->connection = $tenantor->getTenantConnectionName();

        $this->setTable($this->getTenantTable());

        return $this;
    }

    /**
     * Get tenant table name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    abstract public function getTenantTable(): string;
}
