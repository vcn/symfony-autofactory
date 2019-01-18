<?php

namespace Vcn\Symfony\AutoFactory;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerUtils
{
    /**
     * @param ServiceDefinition $serviceDefinition
     * @param ContainerBuilder  $container
     */
    public static function register(ServiceDefinition $serviceDefinition, ContainerBuilder $container)
    {
        $serviceId      = $serviceDefinition->getServiceId();
        $serviceClass   = $serviceDefinition->getServiceClass();
        $factoryClass   = $serviceDefinition->getFactoryClass();
        $factoryMethod  = $serviceDefinition->getFactoryMethod();
        $public         = $serviceDefinition->isPublic();
        $autoconfigured = $serviceDefinition->isAutoconfigured();
        $autowired      = $serviceDefinition->isAutowired();
        $tags           = $serviceDefinition->getTags();
        $bindings       = $serviceDefinition->getBindings();
        $aliases        = $serviceDefinition->getAliases();

        $container
            ->register($serviceId, $serviceClass)
            ->setFactory([$factoryClass, $factoryMethod])
            ->setPublic($public)
            ->setAutoconfigured($autoconfigured)
            ->setAutowired($autowired)
            ->setTags($tags)
            ->setBindings($bindings);

        foreach ($aliases as $aliasId => $aliasIsPublic) {
            $container
                ->setAlias($aliasId, $serviceId)
                ->setPublic($aliasIsPublic);
        }
    }
}
