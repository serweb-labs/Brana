<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\FieldMapping;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class ChoiceFieldMapping extends BranaFieldMappingBase implements BranaFieldMappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMapTypeName()
    {
        return $this->field->internals['data_type'];
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
        return $this->model['nullable'];
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
    public function getMapDefault()
    {
        return $this->model['default'];
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
    public function getMapIsPk()
    {
        return false;
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
        if (is_numeric($value)) {
            return $value + 0;
        }
        return $value;
    }


    /**
     * {@inheritdoc}
     */
    public function dehydrate($value){
        if (is_numeric($value)) {
            return $value + 0;
        }
        return $value;
    }

}
