<?php
namespace Brana\CmfBundle\Store;
use Brana\CmfBundle\Store\StoreInteractorInterface;

interface StoreDriver {
    public function load(Store $store) : void;

    public function getName() : string;
}
