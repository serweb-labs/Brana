<?php
namespace Brana\CmfBundle\Store\Drivers\Orm;

use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Store\IStoreInteractor;
use Brana\CmfBundle\Store\Entity\IBranaEntity;
use Brana\CmfBundle\Store\Query\QuerySet;
use Brana\CmfBundle\Store\QueryPaser;
use Brana\CmfBundle\Store\Drivers\OrmDriver;
use Doctrine\ORM\Query\Expr;

/* execute all queries */
class StoreInteractor implements IStoreInteractor
{
    private $driver;
    private $schema;
    private $store;

    public function __construct(
        OrmDriver $driver,
        SchemaProvider $schema,
        Store $store
    ) {
        $this->driver = $driver;
        $this->schema = $schema;
        $this->store = $store;
    }   


    public function executeQuery(QuerySet $qs)
    {
        $query = $qs->build();
        $contentType = $query['contenttype'];
        $cols = array_keys($this->schema->createSchema()->getTable($contentType)->getColumns());

        $qb = $this->dbalQuery();
        
        $qb->select($cols)
        ->from($contentType);

        $qp = [
            'query' => $query,
            'pre-finish' => [],
            'finish' => [],
            'result' => [],
            'paramsIndex' => 1
        ];

        foreach ($query['members'] as $member) {
            $this->parseBranaQueryMember($member, $qb, $qp);
        }
        foreach ($qp['pre-finish'] as $fn) {
            $fn();
        }
        
        if ($_SERVER['APP_DEBUG']) {
            dump($qb->getSQL());
        }
        
        $results = $qb->execute()->fetchAll();

        foreach ($results as $row) {
            $instance = $this->getManager($contentType)->create();
            $qp['result'][] = $this->hydrate($instance, $row);
        }

        foreach ($qp['finish'] as $fn) {
            $fn();
        }

        return $qp['result'];
    }


    private function parseBranaQueryMember($m, $qb, &$qp)
    {   
        switch ($m['type']) {
            case 'set':
                foreach ($m['members'] as $nm) {
                    $this->parseBranaQueryMember($nm, $qb, $qp);
                }
                break;
            case 'where':
                if ($m['nexo'] === 'and') {
                    $qb->andWhere(new Expr\Comparison($m['expr'][0], $m['expr'][1], ":" . $m['expr'][0]));
                }
                else {
                    $qb->orWhere(new Expr\Comparison($m['expr'][0], $m['expr'][1], ":" . $m['expr'][0]));
                }
                $qb->setParameter($m['expr'][0], $m['expr'][2]);
                break;
            case 'find':
                $metadata = $this->driver->getMetadata()[$qp['query']['contenttype']];
                $pkField = $metadata->getPk();
                $pkCol = $pkField['columnName'];
                $qb->add('where', new Expr\Comparison($pkCol, '=', ':pk'));
                $qb->setParameter('pk', $m['pk']);
                break;
            case 'limit':
                $qp['pre-finish'][] = function() use ($qb, $m) {
                    $qb->setMaxResults($m['value']);
                };
                break;
            case 'offset':
                $qp['pre-finish'][] = function() use ($qb, $m) {
                    $qb->setFirstResult($m['value']);
                };
                break;
            case 'order-by':
                $qp['pre-finish'][] = function() use ($qb, $m) {
                    $qb->orderBy($m['field'], $m['order']);
                };
                break;
            default:
                // $this->runQueryDirective($qp, $member->type);
                break;
        }
    }


    private function runQueryDirective($qb, $directive)
    {
        'Brana\CmfBundle\Store\Query\\'. $directive::parse($qp);
    }


    private function getManager($contentType)
    {
        return $this->store->getManager($contentType);
    }


    private function dbalQuery()
    {
        $conn = $this->driver->getConnection();
        return $conn->createQueryBuilder();
    }


    public function get(string $contentType, $id)
    {
        $qb = $this->dbalQuery();
        $metadata = $this->driver->getMetadata()[$contentType];
        $metadataFields = $metadata->getFieldMappings();
        $instance = $this->getManager($contentType)->create();
        $pkField = $metadata->getPk();
        $pkCol = $pkField['columnName'];

        // columns
        $cols = array_keys($this->schema->createSchema()->getTable($metadata->tableName)->getColumns());

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

    public function create(IBranaEntity $instance)
    {
        $data = $this->dehydrate($instance);
        $contentType = $instance->getContentTypeName();
        $metadata = $this->driver->getMetadata()[$contentType];
        $schema = $metadata->getFieldMappings();

        $params = [];
        $count = 0;
        $cols = [];

        foreach ($schema as $field) {
            $key = $field['fieldName'];
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
    public function update(IBranaEntity $instance)
    {
        $data = $this->dehydrate($instance);
        $contentType = $instance->getContentTypeName();
        $metadata = $this->driver->getMetadata()[$contentType];
        $schema = $metadata->getFieldMappings();
        $cols = [];
        $params = [];
        $count = 0;
        $pkField = $metadata->getPk()['fieldName'];
    
        $qb = $this->dbalQuery()
        ->update($contentType); // TODO set real alias

        foreach ($schema as $field) {
            $key = $field['fieldName'];
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
    public function remove(IBranaEntity $instance)
    {
        $data = $this->dehydrate($instance);
        $contentType = $instance->getContentTypeName();
        $metadata = $this->driver->getMetadata()[$contentType];
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


    public function refresh(IBranaEntity $instance)
    {
        $this->get($instance, $instance->getPk());
        return $instance;
    }


    public function dehydrate(IBranaEntity $instance)
    {
        $values = [];
        $contentType = $instance->getContentTypeName();
        $schema = $this->driver->getMetadata()[$contentType]->getFieldMappings();
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


    public function hydrate(IBranaEntity $instance, array $raw)
    {
        $contentType = $instance->getContentTypeName();
        $schema = $this->driver->getMetadata()[$contentType]->getFieldMappings();
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
