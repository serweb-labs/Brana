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
    private $contenttypes;
    private $driver;

    public function __construct(
        ContentTypesConfig $contenttypes,
        OrmDriver $driver
    ) {
        $this->contenttypes = $contenttypes->get();
        $this->driver = $driver;
    }

    public function executeBranaQuery(Query $qs)
    {
        $query = $qs->toArray();
        $qb = $this->dbalQuery();
        $ct = $query['contenttype'];
        $contenttype = $this->contenttypes[$ct];
        $fields = array_keys($contenttype['fields']);
        $qb->select($fields)
        ->from($ct);
        $qp = new QueryPaser($qb);
        foreach ($query['members'] as $member) {
            $this->parseBranaQueryMember($qp, $member);
        }
        $qp->preFinish();
        $results = $qb->execute();
        $all = [];
        foreach ($results as $row) {
            $instance = $this->entityFactory($ct);
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

    // TODO: centralize instance factory maybe in store
    private function entityFactory($contenttype)
    {
        $klass = $this->driver->store->getEntityClass($contenttype);
        return new $klass($contenttype, []);
    }

    private function dbalQuery()
    {
        $conn = $this->driver->getConnection();
        return $conn->createQueryBuilder();
    }

    public function get(string $contenttype, $id)
    {
        $qb = $this->dbalQuery();
        $metadata = $this->driver->metadata[$contenttype];
        $metadataFields = $metadata->getFieldMappings();
        $instance = $this->entityFactory($contenttype);
        $pkField = $metadata->getPk();
        $pkCol = $pkField['columnName'];

        // columns
        $cols = array_map(function($value){
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
    public function all(string $contenttype)
    {
        $qb = $this->dbalQuery();
        $metadata = $this->driver->metadata[$contenttype];
        $metadataFields = $metadata->getFieldMappings();

        // columns
        $cols = array_map(function($value){
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
                $instance = $this->entityFactory($contenttype);
                $all[] = $this->hydrate($instance, $row);
            }
        }
        return $all;
    }

    public function filter(Query $qs)
    {
        return executeBranaQuery($qs);
    }

    public function create(BranaEntity $entity)
    {
        $data = $this->toInternal($entity);
        $ct = $entity->meta['contenttype'];
        $schema = $this->contenttypes[$ct];
        $cols = [];
        $params = [];
        $count = 0;
        $cols = [];

        foreach ($schema['fields'] as $field) {
            $cols[$field] = '?';
            $params[$field] = [
                'index' => $count,
                'value' => $data[$field]
            ];
            $count++;
        }

        $qb = $this->dbalQueryFactory()
        ->insert($ct)
        ->values($cols);

        foreach ($params as $param) {
            $qb->setParameter($param['index'], $param['value']);
        }

        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        $this->hydrate($entity, $result);
    }

    public function update(BranaEntity $entity)
    {
        $data = $this->toInternal($entity);
        $ct = $entity->meta['contenttype'];
        $schema = $this->contenttypes[$ct];
        $cols = [];
        $params = [];
        $count = 0;
        $cols = [];

        foreach ($schema['fields'] as $field) {
            $cols[$field] = '?';
            $params[$field] = [
                'index' => $count,
                'value' => $data[$field]
            ];
            $count++;
        }

        $qb = $this->dbalQuery()
        ->update($ct)
        ->values($cols);

        foreach ($params as $param) {
            $qb->setParameter($param['index'], $param['value']);
        }

        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        $this->hydrate($entity, $result);
    }

    public function refresh(BranaEntity $instance)
    {
        $this->get($instance, $instance->getPk());
        return $instance;
    }

    public function dehydrate(BranaEntity $instance)
    {
        $entry = [];
        $ct = $instance->meta['contenttype'];
        $schema = $this->contenttypes[$ct];
        foreach ($schema['fields'] as $k => $val) {
            $fieldType = ucwords($val['type']);
            $fieldClass = "Field\Type\\{$fieldType}";
            $data = $fieldClass::dehydrate($raw[$k]);
            $entry[$k] = $data;
        }
        return $entry;
    }

    public function hydrate(BranaEntity $instance, array $raw)
    {
        $ct = $instance->meta['contenttype'];
        $schema = $this->driver->metadata[$ct]->getFieldMappings();
        foreach ($schema as $val) {
            if (isset($raw[$val['columnName']])) {
                $field = $val['_fieldInstance'];
                $branaName = $val['fieldName'];
                $data = $field->hydrate($raw[$branaName]);
                $setter = 'set' . ucwords($branaName);
                if (method_exists($instance, $setter)) {
                    $instance->$setter($data);
                }
                else {
                    try {
                        $instance->$branaName = $data;
                    }
                    catch (Exception $e) {
                        throw $e;
                    }
                }
            }
        }
        return $instance;
    }
}
