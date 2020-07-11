<?php

namespace Brana\CmfBundle\Store\Entity;
use Brana\CmfBundle\Store\Entity\IBranaEntity;

/**
 *
 */
abstract class BaseEntity implements IBranaEntity
{
    protected $contentTypeName;

    public function __construct($data = [])
    {   
        foreach ($data as $prop => $value) {
            if (property_exists($this, $prop)) {
                $this->set($prop, $data[$prop]);
            }
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
        else if (property_exists($this, $prop)) {
            $reflection = new \ReflectionProperty($this, $prop);
            if ($reflection->isPublic()) {
                return $this->$prop;
            }
            throw new \RuntimeException("${$prop}: property is not public.");
        }
    }


    public function set($prop, $value)
    {   
        $setter = 'set' . ucfirst($prop);
        if(method_exists($this, $setter)) {
            return $this->$setter($value);
        }
        else if (property_exists($this, $prop)) {
            $reflection = new \ReflectionProperty($this, $prop);
            if ($reflection->isPublic()) {
                $this->$prop = $value;
                return;
            }
            throw new \RuntimeException("${$prop}: property is not public.");
        }
        else {
            $this->$prop = $value;
        }
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
