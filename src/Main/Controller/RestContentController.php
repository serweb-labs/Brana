<?php
namespace App\Main\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Brana\CmfBundle\Store\Store;
use App\Main\Service\Brana;
use Symfony\Component\HttpFoundation\Request;

class RestContentController extends AbstractController
{
    private $store;
    private $defaultSerializerClass;

    public function __construct(Brana $brana)
    {
        $this->store = $brana->store;
        $this->defaultSerializerClass = \Brana\CmfBundle\Store\Serializer\ContentSerializer::class;
    }


    public function getSerializer()
    {
        $params = $this->getParameter('brana_cmf.rest_content_types');
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $contenttype = $request->get('contenttype');
        $serializerClass = $params[$contenttype]['serializer'] ?? $this->defaultSerializerClass;
        if ($this->store->has($contenttype)) {
            return new $serializerClass(
                $this->store->$contenttype,
                [
                    'request' => $request,
                    'params' => $params
                ]
            );
        }
        throw new \Exception("Serializer Not Found", $slug);
    }


    public function getInstance()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $contenttype = $request->get('contenttype');
        if ($this->store->has($contenttype)) {
            $manager = $this->store->$contenttype;
            $id = $request->get('slug');
            return $manager->get($id);
        }
    }


    public function getQuerySet()
    {
    }


    public function index($contenttype, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $serializer = $this->getSerializer();
            $manager = $this->store->$contenttype;
            $objs = [];
            foreach ($manager->all() as $item) {
                $objs[] =  $serializer->retrieve($item)['data'];
            }
            return new JsonResponse(array('data' => $objs));
        }
        return new JsonResponse(array('errors' => ["{$contenttype}: resource not found"]), 404);
    }


    public function retrieve($contenttype, $slug, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $serializer = $this->getSerializer();
            $manager = $this->store->$contenttype;
            $item = $manager->get($slug);
            if (!isset($item)) {
                throw new \Exception("{$contenttype}/{$slug}: resource not found");
            }
            $data = $serializer->retrieve($item);
            return new JsonResponse($data);
        }
        return new JsonResponse(array('errors' => ["{$contenttype}/{$slug}: resource not found"]), 404);
    }


    public function create($contenttype, Request $request)
    {
        if ($this->store->has($contenttype)) {
            $serializer = $this->getSerializer();
            $data = $serializer->create();
            if (0 !== count($data['errors'])) {
                return new JsonResponse($data['errors'], 400);
            }
            return new JsonResponse($data['data'], 201);
        }
        return new JsonResponse(array('errors' => ["{$contenttype}: resource not found"]), 404);
    }


    public function update($contenttype, $slug, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $serializer = $this->getSerializer();
            $manager = $this->store->$contenttype;
            $instance =  $manager->create(['id'=>(integer) $slug]);
            $result = $serializer->update($instance);
            if (0 !== count($result['errors'])) {
                return new JsonResponse($result['errors'], 400);
            }
            return new JsonResponse($result['data'], 200);
        }
        return new JsonResponse(array('errors' => ["{$contenttype}/{$slug}: resource not found"]), 404);
    }


    public function partialUpdate($contenttype, $slug, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $instance = $this->getInstance();
            $serializer = $this->getSerializer();
            $manager = $this->store->$contenttype;
            $result = $serializer->update($instance);
            if (0 !== count($result['errors'])) {
                return new JsonResponse($result['errors'], 400);
            }
            return new JsonResponse($result['data'], 200);
        }
        return new JsonResponse(array('errors' => ["{$contenttype}/{$slug}: resource not found"]), 404);
    }


    public function destroy($contenttype, $slug, Request $request)
    {   
        if ($this->store->has($contenttype)) {
            $instance = $this->getInstance();
            if ($instance) {
                $manager = $this->store->$contenttype;
                $result = $manager->remove($instance) ? ['success'=> true] : ['success'=> false]; 
                $code = $result['success'] ? 204 : 400; 
                return new JsonResponse($result, $code);
            }
        }
        return new JsonResponse(array('errors' => ["{$contenttype}/{$slug}: resource not found"]), 404);
    }

}