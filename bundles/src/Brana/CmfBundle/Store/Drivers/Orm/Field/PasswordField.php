<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class PasswordField implements BranaFieldInterface
{

    public function __construct(array $config, $name)
    {
        $fallback = [
            'nullable' => true,
            'length' => 512
        ];
        $this->config = array_merge($fallback, $config);
        $this->config['name'] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'password';
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
        return 'text';
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
