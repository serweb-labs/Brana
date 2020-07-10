<?php
namespace Brana\CmfBundle\Store\Serializer\Field;

interface IBranaFieldSerializer
{

    public static function toRepresentation($value, array $options);

    public static function toInternal($value, array $options);
}

