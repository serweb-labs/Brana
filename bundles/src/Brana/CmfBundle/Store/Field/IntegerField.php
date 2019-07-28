<?php

namespace Brana\CmfBundle\Store\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class IntegerField implements BranaFieldInterface
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
        return 'integer';
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultOptions(): array
    {
        return [
            'nullable' => true,
            'length' => 10,
            'pk' => false
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModel(): array {
        return $this->model;
    }
}
