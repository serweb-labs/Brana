<?php

namespace Brana\CmfBundle\Auth;

use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Brana\CmfBundle\Store\Store;

/**
 * Authentication handling.
 *
 * @author Luciano Rodriguez <luciano.rdz@gmail.com>
 */
class JwtBearerAuth // implements AuthProviderInterface
{   
    public function __construct(string $privateKey, string $publicKey, Store $store)
    {
        $this->store = $store;
        $this->config = [
            "private_key" => $privateKey,
            "public_key" => $publicKey,
            "algorithm" => "RS256",
            "window_time" => 300
        ];
    }


    public function login($user, $password)
    {   
        // get user and check password
        $user = $this->store->users->get($user);
        $hash = $user->getHash();
        if (!password_verify($password, $hash)) {
            return false;
        } 
        $time = time();
        $payload = array(
            "iat" => $time,
            "exp" =>  $time + $this->config["window_time"],
            'data' => [
                'uid' => $user
            ]
        );

        $token = JWT::encode($payload, $this->config["private_key"], $this->config["algorithm"]);
        return $token;
    }


    public function whoami(string $token)
    {
        $result = $this->isAuthorized($token);
        if ($result['success']) {
            return $result['data']->data->uid;
        }
        return 0;
    }

    public function isAuthorized($token)
    {
        // Validate Token
        try {
            $data = JWT::decode($token, $this->config["public_key"], array($this->config["algorithm"]));
            $result = array(
                'success' => true,
                'data' => $data
            );
        } catch (\Exception $e) {
            if ($e instanceof ExpiredException) {
                $error = "expired";
            }
            $result = array(
                'success' => false,
                'error' => $error
            );
        }
        return $result;

    }


}
