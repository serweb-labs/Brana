<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class SlugField extends TextField
{

    /**
     * {@inheritdoc}
     */
    public function getMapIsUnique()
    {
        return true;
    }

}
