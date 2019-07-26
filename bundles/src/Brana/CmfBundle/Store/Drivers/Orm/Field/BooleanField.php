<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class BooleanField extends BranaFieldBase implements BranaFieldInterface
{

    public function __construct(array $config, $name)
    {
        $fallback = [
            'nullable' => true,
            'length' => null, // TODO
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
    public function getMapIsNullable()
    {
        return $this->config['nullable'];
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
        return (boolean) $this->config['default'];
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


    public function hydrate($value)
    {
        return (bool) $value;
    }


    public function dehydrate($value)
    {
        return (integer) $value;
    }
}
