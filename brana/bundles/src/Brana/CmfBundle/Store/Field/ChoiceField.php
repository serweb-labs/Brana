<?php

namespace Brana\CmfBundle\Store\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class ChoiceField implements IBranaField
{
    public $internals = [];

    public function __construct(array $model, $name)
    {
        $fallback = self::defaultOptions();
        $this->model = array_merge($fallback, $model);
        $this->model['name'] = $name;

        $map = [];
        if ($this->isMulti($this->model['values'])) {
            foreach ($this->model['values'] as $val) {
                $map[$val['name']] = $val['value'];
            }
        }
        else if ($this->isAssoc($this->model['values'])) {
            $map = $this->model['values'];
        }
        else {
            foreach ($this->model['values'] as $val) {
                $map[$val] = $val;
            }
        }
        $this->model['values'] = $map;

        $this->internals['data_type'] = 'integer';
        foreach (array_values($this->model['values']) as $value) {
            if (!is_int($value)) {
                $this->internals['data_type'] = 'string';
                break;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {   
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultOptions(): array
    {
        return [
            'nullable' => true,
            'values' => [],
            'length' => 128
        ];
    }

   /**
     * {@inheritdoc}
     */
    public function getModel(): array {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function isMulti(array $array) {
        $rv = array_filter($array,'is_array');
        if(count($rv)>0) return true;
        return false;
    }

    /* helpers */
    /**
     * {@inheritdoc}
     */
    public function isAssoc(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionByValue($value)
    { 
        $result = array_filter($this->model['values'], function($v) use ($value) {
            return $v === $value;
        });
        if (count($result)) {
            $key = array_keys($result)[0];
            return [
                'name' => $key,
                'value' => $result[$key]
            ];
        }
        return null;
    }


}
