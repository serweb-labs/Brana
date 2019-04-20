<?php
namespace Brana\CmfBundle\Store\Drivers\Orm;

use Brana\CmfBundle\Store\StoreInteractorInterface;
use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;
use Brana\CmfBundle\Store\Query;
use Brana\CmfBundle\Store\QueryPaser;
use Brana\CmfBundle\Psr\ContentTypesConfig;
use Brana\CmfBundle\Store\Drivers\OrmDriver;

/* execute all queries */
class StoreInteractor implements StoreInteractorInterface
{
    private $contentTypes;
    private $driver;

    public function __construct(
        ContentTypesConfig $contentTypes,
        OrmDriver $driver
    ) {
        $this->contentTypes = $contentTypes->get();
        $this->driver = $driver;
    }


    public function executeBranaQuery(Query $qs)
    {
        $query = $qs->toArray();
        $qb = $this->dbalQuery();
        $contentType = $query['contenttype'];
        $contentType = $this->contentTypes[$contentType];
        $fields = array_keys($contentType['fields']);
        $qb->select($fields)
        ->from($contentType);
        $qp = new QueryPaser($qb);
        foreach ($query['members'] as $member) {
            $this->parseBranaQueryMember($qp, $member);
        }
        $qp->preFinish();
        $results = $qb->execute();
        $all = [];
        foreach ($results as $row) {
            $instance = $this->getManager($contentType)->create();
            $all[] = $this->hydrate($instance, $row);
        }
        $qp->onFinish($all);
        return $all;
    }


    private function parseBranaQueryMember($qp, $member)
    {
        switch ($member->type) {
            case 'member':
                foreach ($member['members'] as $member) {
                    $this->parseBranaQueryMember($qp, $member);
                }
                break;
            default:
                $this->runQueryDirective($qp, $member->type);
                break;
        }
    }


    private function runQueryDirective($qb, $directive)
    {
        'Brana\CmfBundle\Store\Query\\'. $directive::parse($qp);
    }


    private function getManager($contentType)
    {
        return$this->driver->store->getManager($contentType);
    }


    private function dbalQuery()
    {
        $conn = $this->driver->getConnection();
        return $conn->createQueryBuilder();
    }


    public function get(string $contentType, $id)
    {
        $qb = $this->dbalQuery();
        $metadata = $this->driver->metadata[$contentType];
        $metadataFields = $metadata->getFieldMappings();
        $instance = $this->getManager($contentType)->create();
        $pkField = $metadata->getPk();
        $pkCol = $pkField['columnName'];

        // columns
        $cols = array_map(function ($value) {
            return $value['columnName'];
        }, $metadataFields);

        $result = $qb
            ->select($cols)
            ->from($metadata->tableName)
            ->where("${pkCol} = :id")
            ->setParameter('id', $id)
            ->execute()
            ->fetch();

        if ($result) {
            $this->hydrate($instance, $result);
            return $instance;
        }
        return null;
    }


    // TODO: avoid select *
    public function all(string $contentType)
    {
        $qb = $this->dbalQuery();
        $metadata = $this->driver->metadata[$contentType];
        $metadataFields = $metadata->getFieldMappings();

        // columns
        $cols = array_map(function ($value) {
            return $value['columnName'];
        }, $metadataFields);

        $result = $qb
            ->select($cols)
            ->from($metadata->tableName)
            ->execute()
            ->fetchAll();

        $all = [];
        if ($result) {
            foreach ($result as $row) {
                $instance = $this->getManager($contentType)->create();
                $all[] = $this->hydrate($instance, $row);
            }
        }
        return $all;
    }


    public function filter(Query $qs)
    {
        return executeBranaQuery($qs);
    }


    public function create(BranaEntity $instance)
    {
        $data = $this->dehydrate($instance);
        $contentType = $instance->getContentTypeName();
        $metadata = $this->driver->metadata[$contentType];
        $schema = $this->contentTypes[$contentType];
        $cols = [];
        $params = [];
        $count = 0;
        $cols = [];

        foreach ($schema['fields'] as $key => $value) {
            if (isset($data[$key])) {
                $cols[$key] = '?';
                $params[$key] = [
                    'index' => $count,
                    'value' => $data[$key]
                ];
                $count++;
            }
        }

        $qb = $this->dbalQuery()
        ->insert($contentType)
        ->values($cols);

        foreach ($params as $param) {
            $qb->setParameter($param['index'], $param['value']);
        }
        $result = $qb->execute();

        // TODO: need support for
        // non auto-increment PKs
        // for ex. uuid
        if ($result === 1) {
            $instance->set($metadata->getPk()['fieldName'], $this->driver->getConnection()->lastInsertId());
        } else {
            throw new \Exception("driver error");
        }
    }

    // TODO: handle no modified result
    public function update(BranaEntity $instance)
    {
        $data = $this->dehydrate($instance);
        $contentType = $instance->getContentTypeName();
        $metadata = $this->driver->metadata[$contentType];
        $schema = $this->contentTypes[$contentType];
        $cols = [];
        $params = [];
        $count = 0;
        $pkField = $metadata->getPk()['fieldName'];
    
        $qb = $this->dbalQuery()
        ->update($contentType); // TODO set real alias

        foreach ($schema['fields'] as $key => $value) {
            if (isset($data[$key]) && $key !== $pkField) {
                $qb->set($key, '?');
                $params[$key] = [
                    'index' => $count,
                    'value' => $data[$key]
                ];
                $count++;
            }
        }
        $qb->where($pkField . " = " . $instance->get($pkField));
        
        foreach ($params as $param) {
            $qb->setParameter($param['index'], $param['value']);
        }
        // dump($instance); die;
        // dump($qb->getSQL()); die;
        $result = $qb->execute();

        if ($result !== 1) {
            throw new \Exception("driver error");
        }
    }



    // TODO: handle no modified result
    public function remove(BranaEntity $instance)
    {
        $data = $this->dehydrate($instance);
        $contentType = $instance->getContentTypeName();
        $metadata = $this->driver->metadata[$contentType];
        $schema = $this->contentTypes[$contentType];
        $cols = [];
        $params = [];
        $count = 0;
        $pkField = $metadata->getPk()['fieldName'];
    
        $qb = $this->dbalQuery()
        ->delete($contentType)
        ->where($pkField . " = :pk")
        ->setParameter(":pk", $instance->get($pkField));

        $result = $qb->execute();

        if ($result !== 1) {
            throw new \Exception("driver error");
        }
        
        return true;
    }


    public function refresh(BranaEntity $instance)
    {
        $this->get($instance, $instance->getPk());
        return $instance;
    }


    public function dehydrate(BranaEntity $instance)
    {
        $values = [];
        $contentType = $instance->getContentTypeName();
        $schema = $this->driver->metadata[$contentType]->getFieldMappings();
        foreach ($schema as $val) {
            $col = $val['columnName'];
            $prop = $val['fieldName'];
            if (null !== $instance->get($prop)) {
                $field = $val['_fieldInstance'];
                $values[$col] = $field->dehydrate($instance->get($prop));
            }
        }
        return $values;
    }


    public function hydrate(BranaEntity $instance, array $raw)
    {
        $contentType = $instance->getContentTypeName();
        $schema = $this->driver->metadata[$contentType]->getFieldMappings();
        foreach ($schema as $val) {
            if (isset($raw[$val['columnName']])) {
                $field = $val['_fieldInstance'];
                $branaName = $val['fieldName'];
                $data = $field->hydrate($raw[$branaName]);
                $setter = 'set' . ucwords($branaName);
                if (method_exists($instance, $setter)) {
                    $instance->$setter($data);
                } else {
                    try {
                        $instance->$branaName = $data;
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }
        }
        return $instance;
    }
}
