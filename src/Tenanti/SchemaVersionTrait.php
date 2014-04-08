<?php namespace Orchestra\Tenanti;

trait SchemaVersionTrait
{
    /**
     * Get schema version name value.
     *
     * @return string
     */
    public function getSchemaVersionValue()
    {
        $key = $this->getSchemaVersionKey();

        return $this->getAttribute($key);
    }
}
