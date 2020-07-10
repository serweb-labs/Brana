<?php

namespace Brana\CmfBundle\Store;

use Brana\CmfBundle\CaseTransformTrait;
use Brana\CmfBundle\Store\Entity\BranaEntity;
use Brana\CmfBundle\Store\Manager\ManagerInterface as BranaManager;
use Brana\CmfBundle\Store\Manager;

class Store
{   
    use CaseTransformTrait;

    private $classes = [];
    private $entities = [];

    public function has($contenttype):bool
    {   
        return (
            isset($this->{$contenttype}) && 
            $this->{$contenttype} instanceof BranaManager
        );
    }

    public function register($ctName, $fields, $entityClass, $managerClass, $interactor)
    {   
        $this->{$ctName} = new $managerClass(
            $ctName,
            $fields,
            $entityClass,
            $interactor
        );
        $this->entities[$ctName] = $entityClass;        
        $this->classes[$entityClass] = $this->{$ctName};
    }

    public function getContentTypes() {
        return array_keys($this->entities);
    }

    public function getContentType(string $ct) {
        return array_keys($this->entities);
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
