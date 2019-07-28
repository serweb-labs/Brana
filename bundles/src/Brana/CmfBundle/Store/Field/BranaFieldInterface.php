<?php

namespace Brana\CmfBundle\Store\Field;

/**
 * Interface implemented by content fields.
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
interface BranaFieldInterface
{
    /**
     * Get name.
     *
     * @return string The field name
     */
    public function getName(): string;

    /**
     * Get model.
     *
     * @return array The field output as array
     */
    public function getModel(): array;

    /**
     * Get default options.
     *
     * @return array The field output as array
     */
    public static function defaultOptions(): array;
}
