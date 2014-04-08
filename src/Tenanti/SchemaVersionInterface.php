<?php namespace Orchestra\Tenanti;

interface SchemaVersionInterface
{
    /**
     * Get schema version name.
     *
     * @return string
     */
    public function getSchemaVersionKey();
}
