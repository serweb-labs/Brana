<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Metadata;

use Brana\CmfBundle\Store\Drivers\Orm\NamingStrategy;
use Brana\CmfBundle\Store\Drivers\Orm\INamingStrategy;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as IClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Metadata class
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class ClassMetadata extends ClassMetadataInfo implements IClassMetadata
{
    /** @var string */
    protected $brananame;

    /**
     * Constructor.
     *
     * @param string                  $className      Fully-qualified class name
     * @param INamingStrategy $namingStrategy Naming strategy
     */
    public function __construct($className, INamingStrategy $namingStrategy = null)
    {
        $this->name = $className;
        $this->namingStrategy = $namingStrategy ?: new NamingStrategy();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Allows overriding the Entity Name for this mapping
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the fully-qualified class name of this persistent class.
     *
     * @return string
     */
    public function getTableName()
    {
        if ($this->tableName) {
            return $this->tableName;
        }

        return $this->namingStrategy->classToTableName($this->name);
    }

    /**
     * Sets the table name of this persistent class.
     *
     * @param $tableName
     *
     * @return string
     */
    public function setTableName($tableName)
    {
        return $this->tableName = $tableName;
    }

    /**
     * Gets the brana name of this class.
     *
     * @return string
     */
    public function getBranaName()
    {
        return $this->_brananame;
    }

    /**
     * Sets the brana name
     *
     * @param $name
     *
     * @return string
     */
    public function setBranaName($name)
    {
        return $this->_brananame = $name;
    }

    /**
     * Gets the internal alias using the naming strategy.
     *
     * @return string
     */
    public function getAliasName()
    {
        return $this->namingStrategy->classToAlias($this->name);
    }


    /**
     * Sets the fieldMappings array with metadata.
     *
     * @param array $fieldMappings
     */
    public function setFieldMappings($fieldMappings)
    {
        $this->fieldMappings = $fieldMappings;
    }

    /**
     * Gets the fieldMappings array.
     *
     *
     * @return array $fieldMappings
     */
    public function getFieldMappings()
    {
        return (array) $this->fieldMappings;
    }

    /**
     * Gets the primary key / identifier field metadata.
     *
     *
     * @return array
     */
    public function getPk()
    {
        foreach ($this->fieldMappings as $value) {
            if (isset($value['id']) && $value['id']) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Checks whether the class uses an identity column for the Id generation.
     *
     * @return boolean TRUE if the class uses the IDENTITY generator, FALSE otherwise.
     */
    public function isIdGeneratorIdentity()
    {
        return true;
    }


}
