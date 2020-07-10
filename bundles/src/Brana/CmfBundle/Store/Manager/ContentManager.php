<?php

namespace Brana\CmfBundle\Store\Manager;

use Brana\CmfBundle\Store\Manager\IManager;
use Brana\CmfBundle\Store\Entity\IBranaEntity;
use Brana\CmfBundle\Store\IStoreInteractor;
use Brana\CmfBundle\Store\Query\Query;

/**
 * Manager
 * 
 * Default generic manager for all content types
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class ContentManager implements IManager
{
    private $fields;
    public $entityClass;
    protected $interactor;
    protected $contenttypeName;

    public function __construct(
        string $contenttypeName,
        array $fields,
        string $entityClass,
        IStoreInteractor $interactor
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

    public function update(IBranaEntity $instance)
    {
        return $this->interactor->update($instance);
    }

    public function patch(IBranaEntity $instance)
    {
        return $this->interactor->patch($instance);
    }

    public function save(IBranaEntity $instance)
    {
        if ($instance->get('id')) {
            return $this->interactor->update($instance);
        }
        return $this->interactor->create($instance);
    }

    public function refresh(IBranaEntity $instance)
    {
        return $this->interactor->refresh($instance);
    }

    public function remove(IBranaEntity $instance)
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
