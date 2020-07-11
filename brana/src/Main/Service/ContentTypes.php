<?php

namespace App\Main\Service;

use Symfony\Component\Yaml\Yaml;
use Brana\CmfBundle\Psr\ContentTypesConfig;

class ContentTypes implements ContentTypesConfig
{
    private $data = [];
    private $loaded = false;

    private $fieldClasses =  [
        'text' => 'Brana\CmfBundle\Store\Field\TextField',
        'integer' => 'Brana\CmfBundle\Store\Field\IntegerField',
        'slug' => 'Brana\CmfBundle\Store\Field\SlugField',
        'date' => 'Brana\CmfBundle\Store\Field\DateField',
        'boolean' => 'Brana\CmfBundle\Store\Field\BooleanField',
        'choice' => 'Brana\CmfBundle\Store\Field\ChoiceField',
        'relation' => 'Brana\CmfBundle\Store\Field\RelationField',
    ];

    // use configTrait;
    public function load() {
        if ($this->loaded === false) {
            $dir = __DIR__ . '/../../../config/contenttypes/';
            $files = array_diff(scandir($dir), array('.', '..'));
            $data = [];
            $abstracts = [];

            // TODO: only accept files .yml/.yaml
            foreach ($files as $file) {
                $value = Yaml::parseFile($dir . $file);
                $data = array_merge($data, $value);
            }
            foreach ($data as $ct => $ctV) {
                if (array_key_exists('abstract', $data[$ct]) && $data[$ct]['abstract'] === true) {
                    $abstracts[$data[$ct]['name']] = $data[$ct];
                    unset($data[$ct]);
                }
            }
            foreach ($data as $ct => $ctV) {
                if (!array_key_exists('abstract', $data[$ct])) {
                    if (array_key_exists('extends', $data[$ct])) {
                        $data[$ct] = $this->merge_ct($abstracts[$data[$ct]['extends']], $data[$ct]);
                        unset($data[$ct]['abstract']);
                    }
                    $data[$ct]['_fields'] = $data[$ct]['fields'];
                    foreach ($data[$ct]['_fields'] as $k => $val) {
                        $data[$ct]['fields'][$k] = new $this->fieldClasses[$val['type']]($val, $k);
                    }
                    unset($data[$ct]['_fields']);
                }
            }
            $this->data = $data;
            $this->loaded = true;
        }
    }

    public function get($path = null)
    {   
        $this->load();
        return $this->data;
    }

    public function set($path = null)
    {
        // $this->data;
    }

    public function merge_ct (array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value)
        {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key]))
            {
                $merged [$key] = $this->merge_ct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    public function __invoke()
    {
        return $this->get();
    }

}
