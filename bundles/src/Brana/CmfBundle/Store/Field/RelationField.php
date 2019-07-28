<?php

namespace Brana\CmfBundle\Store\Field;

/**
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class RelationField implements BranaFieldInterface
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
        return 'relation';
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultOptions(): array
    {
        return [
            'nullable' => true,
            'length' => 10,
            'pk' => false,
            'direction' => 'to',
            'relation_type' => null,
            'target' => null
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModel(): array {
        return $this->model;
    }
}
