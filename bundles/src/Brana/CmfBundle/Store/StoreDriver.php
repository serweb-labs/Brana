<?php
namespace Brana\CmfBundle\Store;
use Brana\CmfBundle\Store\StoreInteractorInterface;

interface StoreDriver {
    public function getName() : string;
}
