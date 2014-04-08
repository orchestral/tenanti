<?php namespace Orchestra\Tenanti;

interface SchemaVersionInterface
{
    /**
     * Get schema version name key.
     *
     * @return string
     */
    public function getSchemaVersionKey();
}
