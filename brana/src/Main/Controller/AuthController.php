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
        if (!$content) {
            return new JsonResponse(['success'=> false, 'message' => 'Bad request'], 400);
        }
        if (!\in_array('user', $content) || !\in_array('password', $content)) {
            return new JsonResponse(['success'=> false, 'message' => 'Bad request'], 400);
        }
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
        $uid = 0;
        if (strpos($authorizationHeader, 'Bearer' . " ") !== false) {
            $token = str_replace('Bearer' . " ", "", $authorizationHeader);
            $uid = $this->auth->whoami($token);
        }
        return new JsonResponse(["data"=> ["id" => $uid]]);

    }

}