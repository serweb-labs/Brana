<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class TextField implements BranaFieldInterface
{

    public function __construct(array $config, $name)
    {
        $fallback = [
            'nullable' => true,
            'length' => 256
        ];
        $this->config = array_merge($fallback, $config);
        $this->config['name'] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($value)
    {
        return $value;
    }


    /**
     * {@inheritdoc}
     */
    public function dehydrate($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapTypeName()
    {
        return 'string';
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
        return $this->config['length'];
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
    public function getMapUseDefault(): bool
    {   
      return array_key_exists('default', $this->config);
    }


    /**
     * {@inheritdoc}
     */
    public function getMapDefault()
    {
        return (string) $this->config['default'];
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
     * Returns additional options to be passed to the storage field.
     *
     * @return array An array of options
     */
    public function getMapOptions()
    {
        return [];
    }

}
