<?php

namespace Brana\CmfBundle\Config;

use Symfony\Component\Yaml\Yaml;

class Taxonomies // implements ConfigInterface
{
    private $data = [];
    // use configTrait;
    public function load() {
        $dir = __DIR__ . '/../../config/taxonomies/';
        $files = array_diff(scandir($dir), array('.', '..'));
        // TODO: only accept files .yml
        foreach ($files as $file) {
            $value = Yaml::parseFile($dir . $file);
            $this->data = array_merge($this->data, $value);
        }
    }

    public function get($path = null)
    {
        return $this->data;
    }

    public function set($path = null)
    {
        // $this->data;
    }
}
