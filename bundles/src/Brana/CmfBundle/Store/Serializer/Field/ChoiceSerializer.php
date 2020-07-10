<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class ChoiceSerializer implements IBranaFieldSerializer
{

    public static function toRepresentation($value, array $options)
    {   
        return $options['model']->getOptionByValue($value);
    }

    public static function toInternal($value, array $options)
    {
        return $value['value'] ?? null;
    }

}
