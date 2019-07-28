<?php
namespace Brana\CmfBundle\Store\Drivers\Orm\FieldMapping;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class BooleanFieldMapping extends BranaFieldMappingBase implements BranaFieldMappingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMapTypeName()
    {
        return 'boolean';
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
        return (boolean) $this->model['default'];
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
        return (bool) $value;
    }


    /**
     * {@inheritdoc}
     */
    public function dehydrate($value)
    {
        return (integer) $value;
    }
}
