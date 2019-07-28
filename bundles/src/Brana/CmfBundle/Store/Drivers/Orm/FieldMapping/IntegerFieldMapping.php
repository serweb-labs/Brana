<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\FieldMapping;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class IntegerFieldMapping extends BranaFieldMappingBase implements BranaFieldMappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMapTypeName()
    {
        return 'integer';
    }


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
        return $this->model['length'];
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsNullable()
    {
        return $this->model['pk'] ? false : $this->model['nullable'];
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
      return array_key_exists('default', $this->model);
    }


    /**
     * {@inheritdoc}
     */
    public function getMapDefault()
    {
        return (int) $this->model['default'];
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsPk()
    {
        return (bool) $this->model['pk'];
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsUnique()
    {
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        return (integer) $value;
    }


    /**
     * {@inheritdoc}
     */
    public function dehydrate($value)
    {
        return (integer) $value;
    }

}
