<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\FieldMapping;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class SlugFieldMapping extends TextFieldMapping
{

    /**
     * {@inheritdoc}
     */
    public function getMapIsUnique()
    {
        return true;
    }

}
