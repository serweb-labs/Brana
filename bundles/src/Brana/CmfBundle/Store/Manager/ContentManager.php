<?php

namespace Brana\CmfBundle\Store\Manager;

use Brana\CmfBundle\Store\Manager\ManagerInterface;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;
use Brana\CmfBundle\Store\StoreInteractorInterface as StoreInteractor;
use Brana\CmfBundle\Store\Query\Query;

/**
 * Manager
 * 
 * Default generic manager for all content types
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class ContentManager implements ManagerInterface
{
    private $fields;
    public $entityClass;
    protected $interactor;
    protected $contenttypeName;

    public function __construct(
        string $contenttypeName,
        array $fields,
        string $entityClass,
        StoreInteractor $interactor
    ) {
        $this->contenttypeName = $contenttypeName;
        $this->fields = $fields;
        $this->entityClass = $entityClass;
        $this->interactor = $interactor;
    }

    public function get($id)
    {
        $query = Query::qs()
        ->contentType($this->contenttypeName)
        ->find($id);
        return $this->interactor->executeQuery($query)[0];
    }

    public function all()
    {
        $query = Query::qs()
        ->contentType($this->contenttypeName);
        // ->limit(20)
        // ->offset(12)
        // ->orderBy('id', 'ASC');
        return $this->interactor->executeQuery($query);
    }

    public function page()
    {
        $query = Query::qs()
        ->contentType($this->contenttypeName)
        ->limit(20);

        return $this->interactor->executeQuery($query);
    }

    public function filter(Array $criteria)
    {
        $query = Query::qs()
        ->contentType($this->contenttypeName);

        foreach ($criteria as $key => $value) {
            $query = $query->where($key, '=', $value);
        }

        return $this->interactor->executeQuery($query);
    }


    public function create(array $data = [])
    {   
        $instance = new $this->entityClass($data);
        $instance->setContentTypeName($this->contenttypeName);
        return $instance;
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

    public function getField($name)
    {
        return $this->fields[$name];
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getContentTypeName()
    {
        return $this->contenttypeName;
    }
}
