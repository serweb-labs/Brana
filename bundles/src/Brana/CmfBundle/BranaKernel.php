<?php

namespace Brana\CmfBundle;

use Symfony\Component\Yaml\Yaml;

use Brana\CmfBundle\Psr\ContentTypesConfig;
use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Psr\BranaKernel as BranaKernelInterface;
use Brana\CmfBundle\Store\Entity\BaseEntity;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface;
use Brana\CmfBundle\Store\Manager\ManagerInterface;

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
            
            if (array_key_exists('entity', $val) && class_exists($val['entity'])) {
                $interfaces = class_implements($val['entity']);
                if (isset($interfaces[BranaEntityInterface::class])) {
                    $entity = $val['entity'];
                }
                else {
                    throw new \Exception("Entity class not loaded for {$val['name']}");
                }
            }
            else {
                throw new \Exception("Entity class not declared {$val['name']}");
            }

            if (array_key_exists('manager', $val) && class_exists($val['manager'])) {
                $interfaces = class_implements($val['manager']);
                if (isset($interfaces[ManagerInterface::class])) {
                    $manager = $val['manager'];
                }
                else {
                    throw new \Exception("Manager class not loaded for {$val['name']}");
                }
            }
            else {
                throw new \Exception("Manager class not declared {$val['name']}");
            }

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
