<?php

namespace Brana\CmfBundle\Store\Drivers\Orm;

/**
 * Handles Object to DB naming adjustments.
 */
interface INamingStrategy
{
    /**
     * Takes either a global or absolute class name and returns an underscored table name.
     *
     * @param $className
     *
     * @return string
     */
    public function classToTableName($className);

    /**
     * Returns a short alias for the entity.
     *
     * @param $className
     *
     * @return string
     */
    public function classToAlias($className);
}
