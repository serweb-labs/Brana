<?php

namespace Brana\CmfBundle\Auth;

use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;
use Brana\CmfBundle\Store\Store;

/**
 * Authentication handling.
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class JwtBearerAuth // implements AuthProviderInterface
{   
    public function __construct($privateKey, Store $store)
    {
        $this->privateKey = $privateKey;
        $this->store = $store;
        
    }


    public function login()
    {   

        $token = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

        $token = JWT::encode($token, $this->privateKey, 'RS256');
        return [$token];
    }


    public function whoami()
    {

    }

    public function isAuthenticated()
    {

    }


}
