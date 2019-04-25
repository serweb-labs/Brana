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
       $data = $this->auth->login();
       return new JsonResponse($data);

    }


    public function whoami(Request $request)
    {   
        $data =  $this->auth->whoami();
        return new JsonResponse($data);

    }

}