<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\FieldMapping;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class DateFieldMapping extends BranaFieldMappingBase implements IBranaFieldMapping
{
    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        $datetime = new \DateTime();
        return $datetime->createFromFormat('Y-m-d', $value);
    }


    /**
     * {@inheritdoc}
     */
    public function dehydrate($value)
    { 
        return $value->format('Y-m-d');
    }


    /**
     * {@inheritdoc}
     */
    public function getMapTypeName()
    {
        return 'date';
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
        return null;
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
    public function getMapUseDefault(): bool
    {   
      return array_key_exists('default', $this->model);
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

}
