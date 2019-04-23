<?php

namespace Brana\CmfBundle;

use Symfony\Component\Yaml\Yaml;

use Brana\CmfBundle\Psr\ContentTypesConfig;
use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Store\Entity\ContentEntity;
use Brana\CmfBundle\Store\Manager\ContentManager;
use Brana\CmfBundle\Psr\BranaKernel as BranaKernelInterface;

abstract class BranaKernel implements BranaKernelInterface
{
    public $store;
    public $contenttypes;

    public function __construct(Store $store, ContentTypesConfig $contenttypes)
    {   
        $this->store = $store;
        $this->contenttypes = $contenttypes;
        $this->setBrana();
        $this->boot();
    }

    public function boot()
    {
        $this->loadStore();
        $this->setStore();
        $this->setConnection();
        $this->bootDriver();
    }

    public function loadStore() 
    {
        $cts = $this->contenttypes->get();
        foreach ($cts as $key => $val) {
            $entity = $val['entity'] ?? ContentEntity::class;
            $manager = $val['manager'] ?? ContentManager::class;
            $this->store->set($key, [
                'entity' => $entity,
                'manager' => $manager
            ]);
        }

    }

    public function setBrana()
    {
        // config store
    }

    public function setStore()
    {
        // config store
    }

    public function setConnection()
    {
        // config drivers params
    }

    public function bootDriver()
    {
        $this->store->loadDriver();
    }

}
