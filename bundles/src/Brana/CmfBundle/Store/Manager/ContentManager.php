<?php

namespace Brana\CmfBundle\Store\Manager;

use Brana\CmfBundle\Store\Manager\ManagerInterface;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;
use Brana\CmfBundle\Store\StoreInteractorInterface as StoreInteractor;

/**
 * Manager
 *
 */
class ContentManager implements ManagerInterface
{
    public function __construct(
        array $contenttype,
        string $entityClass,
        storeInteractor $interactor
    ) {
        $this->contenttype = $contenttype;
        $this->entityClass = $entityClass;
        $this->interactor = $interactor;
    }

    public function get($id)
    {
        return $this->interactor->get($this->contenttype['name'], $id);
    }

    public function filter(array $criteria)
    {
        return $this->interactor->filter($criteria);
    }

    public function all()
    {   
        return $this->interactor->all($this->contenttype['name']);
    }

    public function create(array $data)
    {
        return new $this->entityClass($this->contenttype['name'], $data);
    }

    public function update(BranaEntity $entity)
    {
        return $this->interactor->update($entity);
    }

    public function patch(BranaEntity $entity)
    {
        return $this->interactor->patch($entity);
    }

    public function save(BranaEntity $entity)
    {
        if ($entity->getId()) {
            return $this->interactor->update($entity);
        }
        return $this->interactor->create($entity);
    }

    public function refresh(BranaEntity $entity)
    {
        return $this->interactor->refresh($entity);
    }

    public function getEntity()
    {
        return $this->entityClass;
    }
}
