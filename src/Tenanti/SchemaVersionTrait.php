<?php namespace Orchestra\Tenanti;

trait SchemaVersionTrait
{
    /**
     * Get schema version name value.
     *
     * @return integer
     */
    public function getSchemaVersionValue()
    {
        $key = $this->getSchemaVersionKey();

        return (int) $this->getAttribute($key);
    }
}
