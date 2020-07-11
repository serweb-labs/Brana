<?php
namespace Brana\CmfBundle\Store\Serializer;

class UserSerializer extends ContentSerializer
{
    private $fieldMapping;
    private $params;

    public function getAllKeys()
    {
        return ['email'];
    }
}
