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
            if (!isset($item)) {
                throw new \Exception("Not Found", $slug);
            }
            $data = $serializer->retrieve($item);
            return new JsonResponse($data);
        }
    }

    public function create($contenttype, Request $request)
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
            $data = $serializer->create();
            if (0 !== count($data['errors'])) {
                throw new \Exception("Internal Error", 1);
            }
            return new JsonResponse($data['data']);
        }
    }

    public function update($contenttype, $slug, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $manager = $this->store->$contenttype;
            $instance =  $manager->create(['id'=>(integer) $slug]);
            $params = $this->getParameter('content_serializer_config');
            $serializer = new \Brana\CmfBundle\Store\Serializer\ContentSerializer(
                $manager,
                [
                    'request' => $request,
                    'params' => $params
                ]
            );
            $data = $serializer->update($instance);
            if (0 !== count($data['errors'])) {
                throw new \Exception("Internal Error", 1);
            }
            return new JsonResponse($data['data']);
        }
    }


    public function partialUpdate($contenttype, $slug, Request $request)
    {   
        $instance = $this->getInstance();
        if ($this->store->has($contenttype)) {
            $manager = $this->store->$contenttype;
            $params = $this->getParameter('content_serializer_config');
            $serializer = new \Brana\CmfBundle\Store\Serializer\ContentSerializer(
                $manager,
                [
                    'request' => $request,
                    'params' => $params
                ]
            );
            $data = $serializer->update($instance);
            if (0 !== count($data['errors'])) {
                throw new \Exception("Internal Error", 1);
            }
            return new JsonResponse($data['data']);
        }
    }


    public function destroy($contenttype, $slug, Request $request)
    {   
        $instance = $this->getInstance();
        if ($this->store->has($contenttype)) {
            $manager = $this->store->$contenttype;
            $result = $manager->remove($instance) ? ['success'=> true] : ['success'=> false]; 
            return new JsonResponse($result);
        }
    }


    public function getInstance() {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $contenttype = $request->get('contenttype');
        $id = $request->get('slug');
        if ($this->store->has($contenttype)) {
            return $this->store->{$contenttype}->get($id);
        }
        throw new \Exception("Internal Error");
    }

    public function getQuerySet() {
        
    }

    private function getData($request)
    {
        return json_decode($request->getContent(), true);
    }
}