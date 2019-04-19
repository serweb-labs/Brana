<?php

namespace Brana\CmfBundle\Config;

use Symfony\Component\Yaml\Yaml;

class Brana // implements ConfigInterface
{
    private $data = [];
    private $loaded = false;

    // use configTrait;
    private function load() {
        if ($this->loaded === false) {
            $file = __DIR__ . '/../../config/brana.yaml';
            $value = Yaml::parseFile($file);
            $this->data = $value;
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
}
