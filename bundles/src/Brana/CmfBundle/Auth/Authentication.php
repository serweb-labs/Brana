<?php

namespace Brana\Auth;

use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;
use Brana\CmfBundle\Store\Drivers\Orm\Entity\Entity;
use Brana\CmfBundle\Store\Drivers\Orm\Entity\EntityManagerInterface;
use Brana\CmfBundle\Store\Drivers\Orm\Repository;

/**
 * Authentication handling.
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class JwtBearer implements ProviderInterface
{   

}
