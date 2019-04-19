<?php
namespace App\Main\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Brana\CmfBundle\Store\Store;
use App\Main\Service\Brana;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{   
    public function __construct(Brana $brana)
    {
        $this->store = $brana->store;
    }

    public function index($contenttype, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $params = $this->getParameter('content_serializer_config');
            $serializer = new \Brana\CmfBundle\Store\Serializer\ContentSerializer(
                $this->store->{$contenttype},
                [
                    'request' => $request,
                    'params' => $params
                ]
            );
            $objs = [];
            foreach ($this->store->{$contenttype}->all() as $item) {
                $objs[] = $serializer->retrieve($item)['data'];
            }
        }
        return new JsonResponse(array('data' => $objs));
    }

    public function retrieve($contenttype, $slug, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $params = $this->getParameter('content_serializer_config');
            $serializer = new \Brana\CmfBundle\Store\Serializer\ContentSerializer(
                $this->store->{$contenttype},
                [
                    'request' => $request,
                    'params' => $params
                ]
            );
            $item = $this->store->{$contenttype}->get($slug);
            $data = $serializer->retrieve($item);
            if (!isset($item)) {
                throw new \Exception("Not Found", $slug);
            }
            return new JsonResponse($data);
        }
    }

    public function create($contenttype, Request $request)
    {   
        $data =  $this->getData($request);
        \dump($data); exit;
        if ($this->store->has($contenttype)) {
            $obj = $this->store->{$contenttype}->create($data);
            if (!isset($obj)) {
                throw new \Exception("Not Found", 1);
            }
            return new JsonResponse($obj);
        }
    }

    private function getData($request)
    {
        return json_decode($request->getContent(), true);
    }
}