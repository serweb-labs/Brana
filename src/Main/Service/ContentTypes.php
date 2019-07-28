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

            // TODO: only accept files .yml
            foreach ($files as $file) {
                $value = Yaml::parseFile($dir . $file);
                $data = array_merge($data, $value);
            }
            foreach ($data as $ct => $ctV) {
                $data[$ct]['_fields'] = $data[$ct]['fields'];
                foreach ($data[$ct]['_fields'] as $k => $val) {
                    $data[$ct]['fields'][$k] = new $this->fieldClasses[$val['type']]($val, $k);
                }
                unset($data[$ct]['_fields']);
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

    public function __invoke()
    {
        return $this->get();
    }
}
