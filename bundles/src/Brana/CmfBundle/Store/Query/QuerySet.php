<?php
namespace Brana\CmfBundle\Store\Query;

class QuerySet {

    public $state = [];
    public $contentType;
    public $isRoot = true;
    public $nexo;
    public $nextNexo;

    public function __constructor() {
        $this->contentType = $ct;
        $this->isRoot = true;
        return $this;
    }

    public function contentType($ct) {
        $this->contentType = $ct;
        $this->isRoot = true;
        return $this;
    }

    public function setQuery(QuerySet $querySet) {
        $this->state = $this->state;
        return $this;
    }

    public function fromJson($json) {
        $arr = \json_decode($json);
        $this->state = $arr;
        return $this;
    }

    public function fromArray($arr) {
        $this->state = $arr;
        return $this;
    }

    public function where($a, $b, $c) {
        $where = [
            'type' => 'where',
            'nexo' => $this->getNexo(),
            'expr' => [
                $a,
                $b,
                $c
            ]
        ];
        $this->state[] = [
            'type' => 'where',
            'build' => function() use ($where) {
                return $where;
            }
        ];
        dump($this->state);
        return $this;
    }


    public function find($value) {
        $find = [
            'type' => 'find',
            'nexo' => $this->getNexo(),
            'pk' => $value
        ];
        $this->state[] = [
            'type' => 'find',
            'build' => function() use ($find) {
                return $find;
            }
        ];
        return $this;
    }


    public function or(QuerySet $querySet) {
        if (isset($querySet)) {
            $querySet->nexo = 'or';
            $querySet->isRoot = false;
            $this->state[] = [
                'type' => 'set',
                'build' => $querySet->build
            ];
        }
        else {
            $this->nextNexo = 'or';
        }
        return $this;
    }

    public function and(QuerySet $querySet) {
        if (isset($querySet)) {
            $querySet->nexo = 'and';
            $querySet->isRoot = false;
            $this->state[] = [
                'type' => 'set',
                'build' => $querySet->build
            ];
        }
        else {
            $this->nextNexo = 'and';
        }
        return $this;
    }

    public function limit($value) {
        $limit = [
            'type' => 'limit',
            'nexo' => null,
            'value' => $value
        ];
        $this->state[] = [
            'type' => 'limit',
            'build' => function() use ($limit) {
                return $limit;
            }
        ];
        return $this;
    }

    public function offset($value) {
        $offset = [
            'type' => 'offset',
            'nexo' => null,
            'value' => $value
        ];
        $this->state[] = [
            'type' => 'offset',
            'build' => function() use ($offset) {
                return $offset;
            }
        ];
        return $this;
    }

    public function orderBy($value, $order = 'DESC') {
        $orderBy = [
            'type' => 'order-by',
            'nexo' => null,
            'field' => $value,
            'order' =>  $order
        ];
        $this->state[] = [
            'type' => 'order-by',
            'build' => function() use ($orderBy) {
                return $orderBy;
            }
        ];
        return $this;
    }

    public function getNexo() {
        if (isset($this->nextNexo)) {
            $nextNexo = 'and';
        }
        else {
            $nextNexo = $this->nextNexo;
            $this->nextNexo = null;
        }
        return $nextNexo;
    }

    public function build() {
        $output = [
            'type' => 'set',
            'members' => [],
            'root' => $this->isRoot,
            'contenttype' => null
        ];

        if (isset($this->contentType)) {
            $output['contenttype'] = $this->contentType;
        }

        // subs
        foreach ($this->state as $member) {
            $output['members'][] = $member['build']();
        }
        return $output;
    }

    public function execute(array $args) {
        return $this->interactor->executeQuery($this);
    }
}