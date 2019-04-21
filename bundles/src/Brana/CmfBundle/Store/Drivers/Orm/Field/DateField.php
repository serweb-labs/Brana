<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class DateField implements BranaFieldInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $config, $name)
    {
        $this->config = $config;
        $this->config['name'] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        $datetime = new \DateTime();
        return $datetime->createFromFormat('Y-d-m', $value);
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
        return $config['nullable'] ?? false;
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
        return $this->config['default'] ?? '';
    }


    /**
     * {@inheritdoc}
     */
    public function getMapIsPk()
    {
        return $this->config['name'] === 'id';
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

}
