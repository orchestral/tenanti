<?php namespace Orchestra\Tenanti;

class Migrator extends \Illuminate\Database\Migrations\Migrator
{
    /**
     * Tenant driver name.
     *
     * @var string
     */
    protected $tenantName;

    /**
     * Tenant configuration.
     *
     * @var array
     */
    protected $tenantConfig = array();

    /**
     * Set tenant configuration.
     *
     * @param  array    $config
     * @return Migrator
     */
    public function setTenant($driver, array $config = array())
    {
        $this->tenantName = $driver;
        $this->tenantConfig = $config;

        return $this;
    }
}
