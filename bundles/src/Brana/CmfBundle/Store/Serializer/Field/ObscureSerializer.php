<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class ObscureSerializer // implements BranaSerializerInterface
{

    public static function toRepresentation($value)
    {
        return '*******';
    }


    public static function toInternal($value)
    {
        return $value;
    }

}
