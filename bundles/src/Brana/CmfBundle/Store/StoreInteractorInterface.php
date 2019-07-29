<?php
namespace Brana\CmfBundle\Store;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;
use Brana\CmfBundle\Store\Query\QuerySet;

interface StoreInteractorInterface {

    public function executeQuery(QuerySet $qs);

    public function create(BranaEntity $entity);

    public function update(BranaEntity $entity);
    
    public function refresh(BranaEntity $entity);
}
