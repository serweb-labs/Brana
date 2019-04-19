<?php

namespace Brana\CmfBundle\Store\Entity;

/**
 *
 */
class ContentEntity implements BranaEntityInterface
{
    public $meta;

    public function __construct($contenttype)
    {
        $this->meta['contenttype'] = $contenttype;
    }
    public function getValue($key) {
        if (isset($this->$key)) {
            return $this->$key;
        }
    }

    public function getName() : string
    {
        return $contenttype;
    }



}
