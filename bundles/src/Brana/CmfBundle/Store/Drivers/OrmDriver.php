<?php
namespace Brana\CmfBundle\Store\Drivers;

use Doctrine\DBAL\Driver\Connection;

use Brana\CmfBundle\Psr\ContentTypesConfig;
use Brana\CmfBundle\Store\Drivers\Orm\Metadata\DriverMetadata;
use Brana\CmfBundle\Store\Drivers\Orm\SchemaProvider;
use Brana\CmfBundle\Store\StoreDriver;

class OrmDriver implements StoreDriver
{
    private $connection;
    private $contenttypes;
    private $metadata = null;

    public function __construct(
        DriverMetadata $driverMetadata,
        Connection $connection,
        ContentTypesConfig $contenttypes
    )
    {
        $this->driverMetadata = $driverMetadata;
        $this->connection = $connection;
        $this->contenttypes = $contenttypes;
    }

    public function getMetadata(): array
    {   
        if (!$this->metadata) {
            $this->metadata = [];
            foreach ($this->contenttypes->get() as $ct) {
                if ($ct['engine'] == 'Orm') {
                    $this->metadata[$ct['name']] = $this->driverMetadata->loadMetadataForContenttype($ct);
                }
            }
        }
        return $this->metadata;
    }

    public function getName():string
    {
        return 'Orm';
    }

    public function getConnection():Connection
    {
        return $this->connection;
    }

}
