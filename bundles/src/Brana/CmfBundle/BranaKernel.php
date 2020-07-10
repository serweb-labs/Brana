<?php

namespace Brana\CmfBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Brana\CmfBundle\Psr\ContentTypesConfig;
use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Psr\BranaKernel as IBranaKernel;
use Brana\CmfBundle\Store\Entity\BaseEntity;
use Brana\CmfBundle\Store\Entity\IBranaEntity;
use Brana\CmfBundle\Store\Manager\IManager;

abstract class BranaKernel implements IBranaKernel
{
    public $store;
    public $contenttypes;
    private $container;
    
    public function __construct(
        Store $store,
        ContentTypesConfig $contenttypes,
        ContainerInterface $container
    )
    {   
        $this->container = $container;
        $this->store = $store;
        $this->contenttypes = $contenttypes->get();
        $this->boot();
    }

    public function boot()
    {
        $this->loadStore();
        $this->setStore();
    }

    public function loadStore() 
    {
        foreach ($this->contenttypes as $key => $ct) {
            
            if (array_key_exists('name', $ct) && is_string($ct['name'])) {
                $name = $ct['name'];
            }
            else {
                throw new \Exception("name is mandatory in contenttype {$key}");
            }

            if (array_key_exists('engine', $ct) && is_string($ct['engine'])) {
                $interactorClass = 'Brana\CmfBundle\Store\Drivers\\' . $ct['engine'] . '\StoreInteractor';
                if (class_exists($interactorClass)) {
                    $interactor = $this->container->get($interactorClass);
                }
                else {
                    throw new \Exception("cannot resolve interactor from engine {$ct['engine']} (ct: {$name})");
                }
            }
            else {
                throw new \Exception("engine is mandatory in contenttype {$key}");
            }

            if (array_key_exists('fields', $ct) && is_array($ct['fields'])) {
                $fields = $ct['fields'];
            }
            else {
                throw new \Exception("fields is mandatory in a contenttype");
            }

            if (array_key_exists('entity', $ct) && class_exists($ct['entity'])) {
                $interfaces = class_implements($ct['entity']);
                if (isset($interfaces[IBranaEntity::class])) {
                    $entityClass = $ct['entity'];
                }
                else {
                    throw new \Exception("Entity class not loaded for {$ct['name']}");
                }
            }
            else {
                throw new \Exception("Entity class not declared {$ct['name']}");
            }

            if (array_key_exists('manager', $ct) && class_exists($ct['manager'])) {
                $interfaces = class_implements($ct['manager']);
                if (isset($interfaces[IManager::class])) {
                    $managerClass = $ct['manager'];
                }
                else {
                    throw new \Exception("Manager class not loaded for {$ct['name']}");
                }
            }
            else {
                throw new \Exception("Manager class not declared {$ct['name']}");
            }

            $this->store->register(
                $name, 
                $fields,
                $entityClass,
                $managerClass,
                $interactor
            );
        }

    }

}
