<?php
namespace Brana\CmfBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Brana\CmfBundle\Config\ContentTypes;
use Brana\CmfBundle\Config\Brana;
use Brana\CmfBundle\Orm\Repository\Repository;
use Brana\CmfBundle\Orm\EntityManager;
use Brana\CmfBundle\Store\Store;
use Brana\CmfBundle\Store\Entity;

class DefaultController extends AbstractController
{
    public function index()
    {   

    }
}