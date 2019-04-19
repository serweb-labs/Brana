<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class TextSerializer // implements BranaSerializerInterface
{

    public static function toRepresentation($value)
    {
        return $value . "--";
    }


    public static function toInternal($value)
    {
        return $value;
    }

}
