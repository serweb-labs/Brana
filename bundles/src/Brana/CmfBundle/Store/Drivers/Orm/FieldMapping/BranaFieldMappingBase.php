<?php
namespace Brana\CmfBundle\Store\Drivers\Orm\FieldMapping;

use Brana\CmfBundle\Store\Field\IBranaField;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
abstract class BranaFieldMappingBase implements IBranaFieldMapping
{
    public $model;
    public $field;

    /**
     * {@inheritdoc}
     */
    public function __construct(IBranaField $fieldModel)
    {
        $this->field = $fieldModel;
        $this->model = $fieldModel->getModel();
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {   
        return $this->field->getName();
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
        return (bool) $this->model['pk'] ?? false;
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsUnique()
    {
        return (bool) ($this->model['unique'] ?? false);
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
