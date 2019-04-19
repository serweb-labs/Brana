<?php

namespace Brana\CmfBundle\Store\Drivers\Orm;

use Brana\CmfBundle\CaseTransformTrait;

/**
 * Handles Object to DB naming adjustments.
 */
class NamingStrategy implements NamingStrategyInterface
{
    use CaseTransformTrait;

    public $prefix = '';

    public function __construct($prefix = 'brana_')
    {
        if ($prefix) {
            $this->prefix = $prefix;
        }
    }

    /**
     * Takes either a global or absolute class name and returns an underscored table name.
     *
     * @param string $className
     *
     * @return string
     */
    public function classToTableName($className)
    {
        $className = $this->getRelativeClass($className);
        $className = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $className)), '_');

        return $this->prefix . $className;
    }

    /**
     * Creates an automatic alias from a class name.
     *
     * @param string $className
     *
     * @return string
     */
    public function classToAlias($className)
    {
        $className = $this->getRelativeClass($className);

        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $className)), '_');
    }

    /**
     * Returns a class name with namespaces removed.
     *
     * @param string $className
     *
     * @return string
     */
    public function getRelativeClass($className)
    {
        if (strpos($className, '\\') !== false) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }

        return $className;
    }

     /**
     * {@inheritdoc}
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $this->underscore($propertyName);
    }
}
