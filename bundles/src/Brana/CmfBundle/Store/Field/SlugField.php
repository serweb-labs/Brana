<?php

namespace Brana\CmfBundle\Store\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class SlugField extends TextField
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'slug';
    }

}
