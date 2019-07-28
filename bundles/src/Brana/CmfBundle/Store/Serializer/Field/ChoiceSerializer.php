<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class ChoiceSerializer // implements BranaSerializerInterface
{

    public static function toRepresentation($value, $field)
    {   
        return $field['model']->getOptionByValue($value);
    }

    public static function toInternal($value)
    {
        return $value['value'] ?? null;
    }

}
