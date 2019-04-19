<?php

namespace App\Main\Service;

use Symfony\Component\Yaml\Yaml;
use Brana\CmfBundle\Psr\ContentTypesConfig;

class ContentTypes implements ContentTypesConfig
{
    private $data = [];
    private $loaded = false;

    // use configTrait;
    public function load() {
        if ($this->loaded === false) {
            $dir = __DIR__ . '/../../../config/contenttypes/';
            $files = array_diff(scandir($dir), array('.', '..'));
            // TODO: only accept files .yml
            foreach ($files as $file) {
                $value = Yaml::parseFile($dir . $file);
                $this->data = array_merge($this->data, $value);
            }
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
