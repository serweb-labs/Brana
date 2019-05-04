<?php
namespace App\Main\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Auth\JwtBearerAuth;
use App\Main\Service\Brana;

class AuthController extends AbstractController
{
    private $store;
    private $defaultSerializerClass;


    public function __construct(Brana $brana, JwtBearerAuth $auth)
    {
        $this->store = $brana->store;
        $this->auth = $auth;
    }


    public function login(Request $request)
    {  
        $content = json_decode($request->getContent(), true);
        $result = $this->auth->login($content['user'], $content['password']);
        if ($result !== false) {
            $data = ["data"=> ["success" => true, "token" => $result]];
        }
        else {
            $data = ["data"=> ["success" => false]];
        }
        return new JsonResponse($data);
    }


    public function whoami(Request $request)
    {   
        $authorizationHeader = $request->headers->get('authorization');
        if (strpos($authorizationHeader, 'Bearer' . " ") !== false) {
            $token = str_replace('Bearer' . " ", "", $authorizationHeader);
        }
        $uid = $this->auth->whoami($token);
        $data = ["data"=> ["id" => $uid]];
        return new JsonResponse($data);

    }

}