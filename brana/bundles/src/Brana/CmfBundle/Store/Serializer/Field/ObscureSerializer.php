<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class ObscureSerializer implements IBranaFieldSerializer
{

    public static function toRepresentation($value, array $options)
    {
        return '*******';
    }


    public static function toInternal($value, array $options)
    {
        return $value;
    }

}
