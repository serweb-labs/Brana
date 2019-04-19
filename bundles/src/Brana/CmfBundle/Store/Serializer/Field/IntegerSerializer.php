<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class IntegerSerializer // implements BranaSerializerInterface
{

    public static function toRepresentation($value)
    {
        return $value;
    }


    public static function toInternal($value)
    {
        return $value;
    }

}
