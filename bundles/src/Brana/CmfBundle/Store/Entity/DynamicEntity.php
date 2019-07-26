<?php

namespace Brana\CmfBundle\Store\Entity;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface;

/**
 *
 */
class DynamicEntity implements BranaEntityInterface
{
    protected $meta;

    public function __construct($contentType, $data)
    {   
        $this->meta = $contentType;
        if (isset($contentType['fields'])) {
            foreach ($contentType['fields'] as $prop => $value) {
                if (array_key_exists($prop, $data)) {
                    $this->set($prop, $data[$prop]);
                }
            }
        }
    }


    public function getContentTypeName():String
    {   
        return $this->meta['name'];
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
