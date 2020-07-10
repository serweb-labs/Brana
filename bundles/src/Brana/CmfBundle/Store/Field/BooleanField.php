<?php

namespace Brana\CmfBundle\Store\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class BooleanField implements IBranaField
{
    public function __construct(array $model, $name)
    {
        $fallback = self::defaultOptions();
        $this->model = array_merge($fallback, $model);
        $this->model['name'] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'boolean';
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultOptions(): array
    {
        return [
            'nullable' => true,
            'length' => null, // TODO
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModel(): array {
        return $this->model;
    }

}
