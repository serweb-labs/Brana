<?php

namespace Brana\CmfBundle\Store\Entity;
use Brana\CmfBundle\Store\Entity\IBranaEntity;

/**
 *
 */
class DynamicEntity implements IBranaEntity
{
    protected $contentTypeName;

    public function __construct($data)
    {   
        foreach ($data as $prop => $value) {
            $this->set($prop, $data[$prop]);
        }
    }


    public function getContentTypeName():String
    {   
        return $this->contentTypeName;
    }


    public function setContentTypeName($name):void
    {   
        $this->contentTypeName = $name;
    }


    public function get($prop)
    {
        $getter = 'get' . ucfirst($prop);
        if(method_exists($this, $getter)) {
            return $this->$getter();
        }
        else if (isset($this->$prop)) {
            return $this->$prop;
        }
    }


    public function set($prop, $value)
    {   
        $setter = 'set' . ucfirst($prop);
        if(method_exists($this, $setter)) {
            return $this->$setter($value);
        }
        $this->$prop = $value;
    }


    public function __get($prop)
    {
        return $this->get($prop);
    }


    public function __set($prop, $value)
    {   
        return $this->set($prop, $value);
    }

}
