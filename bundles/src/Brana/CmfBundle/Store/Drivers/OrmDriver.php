<?php
namespace Brana\CmfBundle\Store\Drivers;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;

use Brana\CmfBundle\Store\Drivers\Orm\Mapping\MetadataDriver;
use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Store\StoreDriver;
use Brana\CmfBundle\Store\StoreInteractorInterface;
use Brana\CmfBundle\Store\Drivers\Orm\StoreInteractor;

class OrmDriver implements StoreDriver
{
    public $metadata = [];
    private $schema = [];
    private $connection;
    public $store;

    public function __construct(MetadataDriver $metadataDriver)
    {
        $this->metadataDriver = $metadataDriver;
        $this->metadata = [];
    }

    public function load(Store $store):void
    {
        $this->store = $store;
        $config = new Configuration();

        $params = array(
            'dbname' => 'brana',
            'user' => 'brana',
            'password' => 'hackm3',
            'host' => '127.0.0.1',
            'driver' => 'pdo_mysql',
        );

        $this->connection = DriverManager::getConnection($params, $config);

        foreach ($store->getContentTypes() as $key) {
            $this->metadata[$key] = $this->metadataDriver->loadMetadataForContenttype($key);
            $this->schema[$key] = $this->loadSchemaFromMetadata($this->metadata[$key]);
        }
    }

    public function getName():string
    {
        return 'orm';
    }

    public function loadSchemaFromMetadata($md)
    {
        return [];
    }

    public function getConnection():Connection
    {
        return $this->connection;
    }
}
