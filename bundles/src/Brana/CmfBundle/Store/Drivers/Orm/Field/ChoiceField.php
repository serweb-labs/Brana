<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class ChoiceField extends BranaFieldBase implements BranaFieldInterface
{
    private $internals = [];
    public function __construct(array $config, $name)
    {
        $fallback = [
            'nullable' => true,
            'values' => [],
            'length' => 128
        ];
        $this->config = array_merge($fallback, $config);
        $this->config['name'] = $name;

        $map = [];
        if ($this->isMulti($this->config['values'])) {
            foreach ($this->config['values'] as $val) {
                $map[$val['name']] = $val['value'];
            }
        }
        else if ($this->isAssoc($this->config['values'])) {
            $map = $this->config['values'];
        }
        else {
            foreach ($this->config['values'] as $val) {
                $map[$val] = $val;
            }
        }
        $this->config['values'] = $map;

        $this->internals['data_type'] = 'integer';
        foreach (array_values($this->config['values']) as $value) {
            if (!is_int($value)) {
                $this->internals['data_type'] = 'string';
                break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {   
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getMapTypeName()
    {
        return $this->internals['data_type'];
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
        return $this->config['default'];
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


    public function hydrate($value)
    {
        $val = array_flip($this->config['values'])[$value];
        if (is_numeric($val)) {
            return (integer) $val;
        }
        return $val;
    }


    public function dehydrate($value)
    {
        return $this->config['values'][$value] ?? null;
    }


    /* helpers */
    public function isAssoc(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }


    public function isMulti(array $array) {
        $rv = array_filter($array,'is_array');
        if(count($rv)>0) return true;
        return false;
    }
}
