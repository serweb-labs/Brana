<?php

namespace Brana\CmfBundle\Store\Manager;

use Brana\CmfBundle\Store\Manager\ManagerInterface;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;
use Brana\CmfBundle\Store\StoreInteractorInterface as StoreInteractor;

/**
 * Manager
 * 
 * Default generic manager for all content types
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class ContentManager implements ManagerInterface
{
    public $contentType;
    public $entityClass;
    protected $interactor;
    protected $name;

    public function __construct(
        array $contentType,
        string $entityClass,
        StoreInteractor $interactor
    ) {
        $this->contentType = $contentType;
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
        return new $this->entityClass($this->contentType, $data);
    }

    public function update(BranaEntity $instance)
    {
        return $this->interactor->update($instance);
    }

    public function patch(BranaEntity $instance)
    {
        return $this->interactor->patch($instance);
    }

    public function save(BranaEntity $instance)
    {
        if ($instance->get('id')) {
            return $this->interactor->update($instance);
        }
        return $this->interactor->create($instance);
    }

    public function refresh(BranaEntity $instance)
    {
        return $this->interactor->refresh($instance);
    }

    public function remove(BranaEntity $instance)
    {
        return $this->interactor->remove($instance);
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getContentTypeName()
    {
        return $this->contentType['name'];
    }
}
