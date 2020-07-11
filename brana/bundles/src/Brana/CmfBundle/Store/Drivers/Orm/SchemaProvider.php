<?php
namespace Brana\CmfBundle\Store\Drivers\Orm;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProviderInterface;
use Doctrine\DBAL\Schema\Table;
use Brana\CmfBundle\Store\Drivers\OrmDriver;
use Brana\CmfBundle\Psr\BranaKernel;

final class SchemaProvider implements SchemaProviderInterface
{
    private $driver;
    private $schema;

    public function __construct(OrmDriver $driver)
    {
        $this->driver = $driver;
        $this->schema = new Schema();
    }

    public function createSchema(): Schema
    {
        foreach ($this->driver->getMetadata() as $ctMeta) {
            $this->loadSchemaFromMetadata($this->schema, $ctMeta);
        }
        return $this->schema;
    }


    public function loadSchemaFromMetadata($schema, $md)
    {
        $table = $schema->createTable($md->getTableName());
        foreach ($md->getFieldMappings() as $fieldK => $fieldV) {
            $this->gatherColumn($md, $fieldV, $table);
        }
    }


    /**
     * Creates a column definition as required by the DBAL from an ORM field mapping definition.
     *
     * @param ClassMetadata $class   The class that owns the field mapping.
     * @param array         $mapping The field mapping.
     * @param Table         $table
     *
     * @return array The portable column definition as required by the DBAL.
     */
    private function gatherColumn($class, array $mapping, Table $table)
    {   

        // prevent add column
        if (isset($mapping['relations'])) {
            switch ($mapping['relations']['relation_type']) {
                case 'one-to-many':
                    # prevent
                    return;
                    break;
                case 'one-to-one':
                    # not implemented yet
                    break;
                case 'many-to-many':
                    # not implemented yet
                    break;
                default:
                    break;
            }

        }

        // add column
        $columnName = $mapping['fieldName'];
        $columnType = $mapping['type'];
        $options = array();
        $options['length'] = isset($mapping['length']) ? $mapping['length'] : null;
        $options['notnull'] = isset($mapping['nullable']) ? ! $mapping['nullable'] : true;

        if (strtolower($columnType) == 'string' && $options['length'] === null) {
            $options['length'] = 255;
        }
        if (isset($mapping['precision'])) {
            $options['precision'] = $mapping['precision'];
        }
        if (isset($mapping['scale'])) {
            $options['scale'] = $mapping['scale'];
        }
        if (isset($mapping['default'])) {
            $options['default'] = $mapping['default'];
        }
        if (isset($mapping['columnDefinition'])) {
            $options['columnDefinition'] = $mapping['columnDefinition'];
        }
        if (isset($mapping['options'])) {
            $knownOptions = array('comment', 'unsigned', 'fixed', 'default');
            foreach ($knownOptions as $knownOption) {
                if (array_key_exists($knownOption, $mapping['options'])) {
                    $options[$knownOption] = $mapping['options'][$knownOption];
                    unset($mapping['options'][$knownOption]);
                }
            }
            $options['customSchemaOptions'] = $mapping['options'];
        }
        if ($class->isIdGeneratorIdentity() && $class->getIdentifierFieldNames() == array($mapping['fieldName'])) {
            $options['autoincrement'] = true;
        }
        if ($class->isInheritanceTypeJoined() && $class->name != $class->rootEntityName) {
            $options['autoincrement'] = false;
        }
        if ($table->hasColumn($columnName)) {
            // required in some inheritance scenarios
            $table->changeColumn($columnName, $options);
        } else {
            $table->addColumn($columnName, $columnType, $options);
        }
        $isUnique = isset($mapping['unique']) ? $mapping['unique'] : false;
        if ($isUnique) {
            $table->addUniqueIndex(array($columnName));
        }

        // TODO: support others Pks
        if (isset($mapping['relations'])) {
            switch ($mapping['relations']['relation_type']) {
                case 'many-to-one':
                    $table->addForeignKeyConstraint($mapping['relations']['target'], array($mapping['fieldName']), array('id'));
                    break;
                case 'one-to-one':
                    if ($mapping['relations']['direction'] === 'to') {
                        $table->addForeignKeyConstraint($mapping['relations']['target'], array($mapping['fieldName']), array('id'));
                    }
                    break;
                case 'many-to-many':
                    # not implemented yet
                    break;
                default:
                    break;
            }
        }


    }
}