<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\FieldMapping;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class RelationFieldMapping extends BranaFieldMappingBase implements BranaFieldMappingInterface
{
    /**
     * {@inheritdoc}
     * TODO: support any primary key
     */
    public function getMapTypeName()
    {
        return 'integer';
    }


    /**
     * {@inheritdoc}
     * TODO: support any primary key
     */
    public function getMapRelations()
    {
        return array(
            'direction' => $this->model['direction'],
            'relation_type' =>$this->model['relation_type'],
            'target' => $this->model['target']
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsNullable()
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
