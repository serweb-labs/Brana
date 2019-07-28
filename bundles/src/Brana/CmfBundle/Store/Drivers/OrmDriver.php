<?php
namespace Brana\CmfBundle\Store\Drivers;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Schema\Schema;

use Brana\CmfBundle\Store\Drivers\Orm\Metadata\DriverMetadata;
use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Store\StoreDriver;
use Brana\CmfBundle\Store\StoreInteractorInterface;
use Brana\CmfBundle\Store\Drivers\Orm\StoreInteractor;

class OrmDriver implements StoreDriver
{
    public $metadata = [];
    private $schema;
    private $connection;
    public $store;


    public function __construct(DriverMetadata $driverMetadata, Connection $connection)
    {
        $this->driverMetadata = $driverMetadata;
        $this->metadata = [];
        $this->connection = $connection;
    }


    public function load(Store $store):void
    {
        $this->store = $store;
        foreach ($store->getContentTypes() as $key) {
            $this->metadata[$key] = $this->driverMetadata->loadMetadataForContenttype($key);
        }
    }


    public function getName():string
    {
        return 'orm';
    }


    public function getConnection():Connection
    {
        return $this->connection;
    }

}
