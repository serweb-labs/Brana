<?php

namespace Brana\CmfBundle\Store\Manager;

use Brana\CmfBundle\Store\Entity\IBranaEntity;

/**
 * Content Manager 
 * 
 */
interface IManager
{
    function get($int);

    function filter(array $filter);
    
    function all();

    function create(array $data);

    function update(IBranaEntity $entity);

    function patch(IBranaEntity $entity);
    
    function save(IBranaEntity $entity);

    function refresh(IBranaEntity $entity);

    function getEntityClass();
}
