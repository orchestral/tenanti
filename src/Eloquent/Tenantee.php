<?php

namespace Orchestra\Tenanti\Eloquent;

use Orchestra\Tenanti\Tenantor;

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
     * @param  \Orchestra\Tenanti\Tenantor  $tenantor
     *
     * @return static
     */
    public static function tenant(Tenantor $tenantor)
    {
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
     * @param  \Orchestra\Tenanti\Tenantor  $tenantor
     *
     * @return $this
     */
    public function setTenantor(Tenantor $tenantor): self
    {
        $this->tenantor = $tenantor;
        $this->connection = $tenantor->getConnectionName();

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
