<?php

namespace Brana\CmfBundle\Store\Entity;

/**
 *
 */
class ContentEntity implements BranaEntityInterface
{
    protected $meta;


    public function __construct($contenttype, $data)
    {   
        $this->meta = $contenttype;
        if (isset($contenttype['fields'])) {
            foreach ($contenttype['fields'] as $prop => $value) {
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
            return $getter();
        }
        else if (isset($this->$prop)) {
            return $this->$prop;
        }
    }


    public function set($prop, $value)
    {   
        $setter = 'set' . ucfirst($prop);
        if(method_exists($this, $setter)) {
            return $setter($value);
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
