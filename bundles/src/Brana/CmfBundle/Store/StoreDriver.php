<?php
namespace Brana\CmfBundle\Store;

interface StoreDriver {
    public function getName() : string;
}
