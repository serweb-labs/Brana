<?php

namespace Brana\CmfBundle\Store\Drivers\Orm\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Brana\CmfBundle\CaseTransformTrait;
use Brana\CmfBundle\Store\Drivers\Orm\Mapping\ClassMetadata as BranaClassMetadata;
use Brana\CmfBundle\Store\Drivers\Orm\NamingStrategy;
use Brana\CmfBundle\Psr\ContentTypesConfig;


/**
 * Brana Metadata Driver
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class MetadataDriver
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
        ContentTypesConfig $contenttypes,
        NamingStrategy $namingStrategy = null
    ) {
        $this->contenttypes = $contenttypes->get();
        $this->namingStrategy = $namingStrategy;
    }


    private function getBranaField($fieldDef, $kField)
    {   
        $map =  [
            'text' => 'Brana\CmfBundle\Store\Drivers\Orm\Field\TextField',
            'integer' => 'Brana\CmfBundle\Store\Drivers\Orm\Field\IntegerField',
            'slug' => 'Brana\CmfBundle\Store\Drivers\Orm\Field\SlugField',
            'date' => 'Brana\CmfBundle\Store\Drivers\Orm\Field\DateField',
            'boolean' => 'Brana\CmfBundle\Store\Drivers\Orm\Field\BooleanField',
            'choice' => 'Brana\CmfBundle\Store\Drivers\Orm\Field\ChoiceField'
        ];
        return new $map[$fieldDef['type']]($fieldDef, $kField);
    }

    public function loadMetadataForContenttype($ctname, $metadata = null)
    {
        $ct = $this->contenttypes[$ctname];

        if ($metadata === null) {            
            $metadata = new BranaClassMetadata($ctname, $this->namingStrategy);
        }

        $culumnName = $this->underscore($ct['name']);
        $metadata->setTableName($culumnName);
        $metadata->setName($ctname);
        $metadata->setIdentifier(['id']); // TODO: choose pk   
        
        foreach ($ct['fields'] as $kField => $vField) {
            $branaField = $this->getBranaField($vField, $kField);
            $metadata->mapField([
                'fieldName'        => $kField,
                'type'             => $branaField->getMapTypeName(),
                'length'           => $branaField->getMapLength(),
                'nullable'         => $branaField->getMapIsNullable(),
                'platformOptions'  => $branaField->getMapPlatformOptions(),
                'precision'        => $branaField->getMapPrecision(),
                'scale'            => $branaField->getMapScale(),
                'default'          => $branaField->getMapDefault(),
                'id'               => $branaField->getMapIsPk(),
                '_fieldtype'       => $branaField->getName(),
                '_fieldInstance'   => $branaField,
            ]);     
        }
        
        $this->metadata[$ctname] = $metadata;
        return $metadata;
    }


}
