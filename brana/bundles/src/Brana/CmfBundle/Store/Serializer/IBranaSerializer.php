<?php
namespace Brana\CmfBundle\Store\Serializer;

use Brana\CmfBundle\Store\Entity\IBranaEntity;

interface IBranaSerializer {
    public function getFields(array $keys);
    public function validateField($field, $value, $constraints);
    public function validator(array $data, $fields, $early);
    public function create();
    public function update(IBranaEntity $instance);
}