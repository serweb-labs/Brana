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
    public $contenttype;
    public $entityClass;
    protected $interactor;
    protected $name;

    public function __construct(
        array $contenttype,
        string $entityClass,
        StoreInteractor $interactor
    ) {
        $this->contenttype = $contenttype;
        $this->entityClass = $entityClass;
        $this->interactor = $interactor;
        $this->name = $this->getContentTypeName();
    }

    public function get($id)
    {
        return $this->interactor->get($this->name, $id);
    }

    public function filter(array $criteria)
    {
        return $this->interactor->filter($criteria);
    }

    public function all()
    {   
        return $this->interactor->all($this->name);
    }

    public function create(array $data = [])
    {
        return new $this->entityClass($this->contenttype, $data);
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
        if ($entity->get('id')) {
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

    public function getContentType()
    {
        return $this->contenttype;
    }

    public function getContentTypeName()
    {
        return $this->contenttype['name'];
    }

}
