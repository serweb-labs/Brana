<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;

/**
 * Interface implemented by content fields.
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
interface BranaFieldInterface
{
    /**
     * Returns the name of the field.
     *
     * @return string The field name
     */
    public function getName();

    /**
     * Returns the path to the template.
     *
     * @return string The template name
     */
    public function getMapTypeName();

    /**
     * Returns the storage type.
     *
     * @throws DBALException
     *
     * @return Type A Valid Storage Type
     */
    public function getMapType();

    /**
     * Returns the name of the field.
     *
     * @return string The field name
     */
    public function hydrate($value);


    /**
     * Returns the name of the field.
     *
     * @return string The field name
     */
    public function dehydrate($value);


    /**
     * Returns the max length
     *
     * @return integer
     */
    public function getMapLength();

    /**
     * Is nullable 
     *
     * @return boolean
     */
    public function getMapIsNullable();

    /**
     * Platform options
     * 
     * @return array
     */
    public function getMapPlatformOptions();

    /**
     * Map precision
     * 
     * @return integer
     */
    public function getMapPrecision();

    /**
     * Map scale
     * 
     * @return integer
     */
    public function getMapScale();

    /**
     * Map default value
     * 
     * @return object
     */
    public function getMapDefault();

    /**
     * Map default value
     * 
     * @return boolean
     */
    public function getMapIsPk();

    /**
     * Returns additional options to be passed to the storage field.
     *
     * @return array An array of options
     */
    public function getMapOptions();

}
