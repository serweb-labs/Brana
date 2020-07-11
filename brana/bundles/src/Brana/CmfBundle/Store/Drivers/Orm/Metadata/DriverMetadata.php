<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Metadata;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Brana\CmfBundle\CaseTransformTrait;
use Brana\CmfBundle\Store\Drivers\Orm\Metadata\ClassMetadata as BranaClassMetadata;
use Brana\CmfBundle\Store\Drivers\Orm\NamingStrategy;

/**
 * Brana Metadata Driver
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class DriverMetadata
{
    use CaseTransformTrait;

    /** @var array */
    protected $contenttypes;

    /** @var array metadata mappings */
    protected $metadata;

    /** @var NamingStrategy */
    protected $namingStrategy;

    /**
     * Constructor.
     *
     * @param ConfigurationValueProxy $contenttypes
     * @param NamingStrategy          $namingStrategy
     */
    public function __construct(
        NamingStrategy $namingStrategy = null
    ) {
        $this->namingStrategy = $namingStrategy;
    }


    private function getBranaField($fieldDef, $kField)
    {   
        $map =  [
            'text' => 'Brana\CmfBundle\Store\Drivers\Orm\FieldMapping\TextFieldMapping',
            'integer' => 'Brana\CmfBundle\Store\Drivers\Orm\FieldMapping\IntegerFieldMapping',
            'slug' => 'Brana\CmfBundle\Store\Drivers\Orm\FieldMapping\SlugFieldMapping',
            'date' => 'Brana\CmfBundle\Store\Drivers\Orm\FieldMapping\DateFieldMapping',
            'boolean' => 'Brana\CmfBundle\Store\Drivers\Orm\FieldMapping\BooleanFieldMapping',
            'choice' => 'Brana\CmfBundle\Store\Drivers\Orm\FieldMapping\ChoiceFieldMapping',
            'relation' => 'Brana\CmfBundle\Store\Drivers\Orm\FieldMapping\RelationFieldMapping',
        ];
        return new $map[$fieldDef->model['type']]($fieldDef, $kField);
    }

    public function loadMetadataForContenttype($ct, $metadata = null)
    {   
        $ctname = $ct['name'];

        if ($metadata === null) {            
            $metadata = new BranaClassMetadata($ctname, $this->namingStrategy);
        }

        $culumnName = $this->underscore($ct['name']);
        $metadata->setTableName($culumnName);
        $metadata->setName($ctname);
        $metadata->setIdentifier(['id']); // TODO: choose pk   
        
        foreach ($ct['fields'] as $kField => $vField) {
            $branaField = $this->getBranaField($vField, $kField, $ctname);
            $mapField = [
                'fieldName'        => $kField,
                'type'             => $branaField->getMapTypeName(),
                'length'           => $branaField->getMapLength(),
                'nullable'         => $branaField->getMapIsNullable(),
                'precision'        => $branaField->getMapPrecision(),
                'scale'            => $branaField->getMapScale(),
                'id'               => $branaField->getMapIsPk(),
                'unique'           => $branaField->getMapIsPk() || $branaField->getMapIsUnique(),
                'relations'        => $branaField->getMapRelations(),
                '_fieldtype'       => $branaField->getName(),
                '_fieldInstance'   => $branaField,
            ];
            if ($branaField->getMapUseDefault()) {
                $mapField['default'] = $branaField->getMapDefault();
            }
            $metadata->mapField($mapField);
        }
        
        $this->metadata[$ctname] = $metadata;
        return $metadata;
    }


}
