<?php
namespace Brana\CmfBundle\Store\Serializer\Field;


class DateSerializer // implements BranaSerializerInterface
{

    public static function getOptions($options)
    {
        $default = [
            'format' => 'm/d/Y'
        ];
        return array_merge($default, $options);
    }


    public static function toRepresentation($value, array $options = [])
    {   
        $options = self::getOptions($options);
        if (isset($value)) {
            return $value->format($options['format']);
        }
        return null;
    }

    // TODO: check format, handle exceptions
    public static function toInternal($value, array $options = [])
    {   
        $options = self::getOptions($options);
        $datetime = new \DateTime();
        return $datetime->createFromFormat($options['format'], $value);
    }

}
