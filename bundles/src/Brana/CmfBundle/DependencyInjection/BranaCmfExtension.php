<?php

namespace Brana\CmfBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class BranaCmfExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {   
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $configMerged = [];

        foreach ($configs as $subConfig) {
            $configMerged = array_merge($configMerged, $subConfig);
        }
        $container->setParameter('content_serializer_config', $configMerged);
    }

}