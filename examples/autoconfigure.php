<?php

use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vcn\Symfony\AutoFactory\Annotation\Autoconfigure;
use Vcn\Symfony\AutoFactory\Annotation\Id;
use Vcn\Symfony\AutoFactory\Annotation\IsPublic;
use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\AutoFactoryParser;
use Vcn\Symfony\AutoFactory\ContainerUtils;

require __DIR__ . '/../vendor/autoload.php';

interface Interfeast
{
}

class Service1 implements Interfeast
{
}

class Service2
{
}

/**
 * @IsPublic(true)
 * @Autoconfigure(true)
 */
class Factory implements AutoFactory
{
    /**
     * @Id("service1.1")
     *
     * @return Service1
     */
    public static function createService1_1(): Service1
    {
        return new Service1();
    }
    /**
     * @Id("service1.2")
     *
     * @return Service1
     */
    public static function createService1_2(): Service1
    {
        return new Service1();
    }

    /**
     * @Id("service2.1")
     *
     * @return Service2
     */
    public static function createService2_1(): Service2
    {
        return new Service2();
    }

    /**
     * @Id("service2.2")
     *
     * @return Service2
     */
    public static function createService2_2(): Service2
    {
        return new Service2();
    }
}

$parser    = new AutoFactoryParser();
$container = new ContainerBuilder();

$container
    ->registerForAutoconfiguration(Interfeast::class)
    ->addTag('custom.tag');

$serviceDefinitions = $parser->parse(Factory::class);

foreach ($serviceDefinitions as $serviceDefinition) {
    ContainerUtils::register($serviceDefinition, $container);
}

$compiler = new Compiler();
$compiler->compile($container);

/**
 * @var OuterService $service2
 */
$serviceIds = $container->findTaggedServiceIds('custom.tag');
foreach ($serviceIds as $serviceId => $tags) {
    echo get_class($container->get($serviceId)) . PHP_EOL;
}



