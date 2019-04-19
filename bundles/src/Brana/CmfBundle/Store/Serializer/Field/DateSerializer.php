<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class DateSerializer // implements BranaSerializerInterface
{

    public static function toRepresentation($value)
    {
        return date("m-d-Y", strtotime($value));
    }


    public static function toInternal($value)
    {
        return date("Y-m-d", strtotime($value));
    }

}
