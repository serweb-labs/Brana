<?php

namespace Brana\CmfBundle\Store\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class DateField implements IBranaField
{
    /**
     * {@inheritdoc}
     */
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
        return 'date';
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultOptions(): array
    {
        return [
            'nullable' => true
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModel(): array {
        return $this->model;
    }

}
