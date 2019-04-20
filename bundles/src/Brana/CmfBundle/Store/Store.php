<?php

namespace Brana\CmfBundle\Store;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Brana\CmfBundle\CaseTransformTrait;
use Brana\CmfBundle\Store\Entity\BranaEntity;
use Brana\CmfBundle\Store\Manager\ManagerInterface as BranaManager;
use Brana\CmfBundle\Store\Manager;
use Brana\CmfBundle\Store\Drivers\BranaDriver;
use Brana\CmfBundle\Psr\ContentTypesConfig;

class Store
{   
    use CaseTransformTrait;

    private $classes = [];
    private $entities = [];
    public $driver;
    public $contenttypes;

    function __construct(ContentTypesConfig $contenttypes, ContainerInterface $container)
    {   
        $driverName = 'Orm';
        $driverClass = 'Brana\CmfBundle\Store\Drivers\\' . $this->camelize($driverName . " Driver");
        $interactorClass = 'Brana\CmfBundle\Store\Drivers\\' . $driverName . '\StoreInteractor';
        $this->contenttypes = $contenttypes;
        $this->driver = $container->get($driverClass);
        $this->interactor = $container->get($interactorClass);
    }

    public function setDriver(BranaDriver $driver)
    {
        $this->driver = $driver;
    }

    public function loadDriver() {
        $this->driver->load($this);
    }

    public function has($contenttype):bool
    {   
        return (
            isset($this->{$contenttype}) && 
            $this->{$contenttype} instanceof BranaManager
        );
    }

    public function register($contenttype, $entity, $manager)
    {   
        $this->{$contenttype} = new $manager(
            $this->contenttypes->get()[$contenttype],
            $entity,
            $this->interactor
        );
        $this->entities[$contenttype] = $entity;        
        $this->classes[$entity] = $this->{$contenttype};
    }

    public function getContentTypes() {
        return array_keys($this->entities);
    }

    public function getContentType(string $ct) {
        return array_keys($this->entities);
    }

    public function set($contenttype, array $data)
    {   
        $this->register($contenttype, $data['entity'], $data['manager']);
    }

    public function getManager($ct) {
        if (is_string($ct)) {
            if ($this->has($ct)) {
                return $this->$ct;
            }
        }
        else if (is_object($ct)) {
            if (isset($classes[get_class($ct)])) {
                return $classes[get_class($ct)];
            }
        }
        throw new \Exception('Not found');
    }
    
    public function getEntityClass(string $ct) {
        if (is_string($ct)) {
            if (isset($this->entities[$ct])) {
                return $this->entities[$ct];
            }
        }
        throw new \Exception('Not found');
    }
}
