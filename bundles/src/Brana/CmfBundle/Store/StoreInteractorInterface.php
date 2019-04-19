<?php
namespace Brana\CmfBundle\Store;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;

interface StoreInteractorInterface {

    public function get(string $contenttype, $pk);

    public function all(string $contenttype);

    public function create(BranaEntity $entity);

    public function update(BranaEntity $entity);
    
    public function refresh(BranaEntity $entity);
}
