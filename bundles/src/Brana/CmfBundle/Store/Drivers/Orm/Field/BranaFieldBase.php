<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
abstract class BranaFieldBase implements BranaFieldInterface
{
    public $config = [];

    /**
     * {@inheritdoc}
     */
    public function getMapType() {
        return Type::getType($this->getMapTypeName());
    }


    /**
     * {@inheritdoc}
     */
    public function getMapLength()
    {
        return $this->config['length'];
    }


    /**
     * {@inheritdoc}
     */
    public function getMapPrecision()
    {
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getMapScale()
    {
        return null;
    }


    /**
     * {@inheritdoc}
     */
    public function getMapUseDefault(): bool
    {   
      return array_key_exists('default', $this->config);
    }


    /**
     * {@inheritdoc}
     */
    public function getMapDefault()
    {
        return $this->config['default'];
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsPk()
    {
        return (bool) $this->config['pk'] ?? false;
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsUnique()
    {
        return (bool) ($this->config['unique'] ?? false);
    }


    /**
     * {@inheritdoc}
     */
    public function getMapRelations()
    {
        return null;
    }


    /**
     * @return array An array of options
     */
    public function getMapOptions()
    {
        return [];
    }


    public function hydrate($value)
    {
        return (integer) $value;
    }


    public function dehydrate($value)
    {
        return (integer) $value;
    }
}
