<?php

namespace Brana\CmfBundle\Config;

use Symfony\Component\Yaml\Yaml;

class TypeMap // implements ConfigInterface
{
    private $data = [];
    // use configTrait;

    public function get($path = null)
    {
        return [
            DBAL\Types\StringType::class   => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\TextType::class,
            DBAL\Types\IntegerType::class  => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\IntegerType::class,
            DBAL\Types\FloatType::class    => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\FloatType::class,
            DBAL\Types\TextType::class     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\TextAreaType::class,
            DBAL\Types\DateType::class     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\DateType::class,
            DBAL\Types\DateTimeType::class => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\DateTimeType::class,
            'block'                        => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\BlockType::class,
            'checkbox'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\CheckboxType::class,
            'date'                         => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\DateType::class,
            'datetime'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\DateTimeType::class,
            'file'                         => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\FileType::class,
            'filelist'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\FileListType::class,
            'float'                        => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\FloatType::class,
            'geolocation'                  => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\GeolocationType::class,
            'hidden'                       => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\HiddenType::class,
            'html'                         => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\HtmlType::class,
            'image'                        => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\ImageType::class,
            'imagelist'                    => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\ImageListType::class,
            'incomingrelation'             => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\IncomingRelationType::class,
            'integer'                      => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\IntegerType::class,
            'markdown'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\MarkdownType::class,
            'embed'                        => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\EmbedType::class,
            'relation'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\RelationType::class,
            'repeater'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\RepeaterType::class,
            'select'                       => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\SelectType::class,
            'selectmultiple'               => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\SelectMultipleType::class,
            'slug'                         => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\SlugType::class,
            'taxonomy'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\TaxonomyType::class,
            'templatefields'               => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\TemplateFieldsType::class,
            'templateselect'               => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\TemplateSelectType::class,
            'text'                         => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\TextType::class,
            'textarea'                     => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\TextAreaType::class,
            'video'                        => Brana\CmfBundle\Store\Drivers\Orm\Field\Type\VideoType::class,
        ];
    }

    public function set($path = null)
    {
        // $this->data;
    }
}
