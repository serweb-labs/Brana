<?php

namespace Brana\CmfBundle\Store\Manager;

use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;

/**
 * Content Manager 
 * 
 */
interface ManagerInterface
{
    function get($int);

    function filter(array $filter);
    
    function all();

    function create(array $data);

    function update(BranaEntity $entity);

    function patch(BranaEntity $entity);
    
    function save(BranaEntity $entity);

    function refresh(BranaEntity $entity);

    function getEntity();
}
