<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class BooleanField implements BranaFieldInterface
{

    public function __construct(array $config, $name)
    {
        $fallback = [
            'default' => false,
            'nullable' => true
        ];
        $this->config = array_merge($fallback, $config);
        $this->config['name'] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {   
        return 'boolean';
    }

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
        return $this->config['nullable'];
    }


    /**
     * {@inheritdoc}
     */
    public function getMapPlatformOptions()
    {
        return null;
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
        return is_bool($this->config['default']) ? $this->config['default'] : false;
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsPk()
    {
        return false;
    }


    /**
     * Returns additional options to be passed to the storage field.
     *
     * @return array An array of options
     */
    public function getMapOptions()
    {
        return [];
    }

    public function hydrate($value)
    {
        return (boolean) $value;
    }

    public function dehydrate($value)
    {
        return (integer) $value;
    }
}