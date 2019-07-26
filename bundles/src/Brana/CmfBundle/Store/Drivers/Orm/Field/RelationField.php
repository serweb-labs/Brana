<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class RelationField extends BranaFieldBase implements BranaFieldInterface
{

    public function __construct(array $config, $name)
    {
        $fallback = [
            'nullable' => true,
            'length' => 10,
            'pk' => false,
            'direction' => 'to',
            'target' => null
        ];
        $this->config = array_merge($fallback, $config);
        $this->config['name'] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {   
        return 'relation';
    }


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
            'direction' => $this->config['direction'],
            'target' => $this->config['target']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMapIsNullable()
    {
        return false;
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
