<?php

namespace App\Main\Service;

use App\Main\Service\ContentType;

use Symfony\Component\Yaml\Yaml;
use Brana\CmfBundle\Store;
use Brana\CmfBundle\Store\Entity\Generic as GenericEntity;
use Brana\CmfBundle\Store\Manager\Generic as ContentManager;
use Brana\CmfBundle\BranaKernel;

class Brana extends BranaKernel
{
    public function setStore() 
    {
        // config store
    }


}
