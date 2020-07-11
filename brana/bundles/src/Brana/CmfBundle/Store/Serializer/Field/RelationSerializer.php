<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class RelationSerializer implements IBranaFieldSerializer
{

    public static function toRepresentation($value, array $options)
    {
        return $value;
    }


    public static function toInternal($value, array $options)
    {
        return $value;
    }

}
