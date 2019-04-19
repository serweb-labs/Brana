<?php
namespace \Brana\CmfBundle\Store\Serializers;

interface BranaSerializerInterface {
    public function getFields();
    public function validateData();
    public function performSerialization();
    public function isValid();
    public function create();
    public function update();
}