<?php

use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vcn\Symfony\AutoFactory\Annotation\Id;
use Vcn\Symfony\AutoFactory\Annotation\IsPublic;
use Vcn\Symfony\AutoFactory\Annotation\Tag;
use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\AutoFactoryParser;
use Vcn\Symfony\AutoFactory\ContainerUtils;

require __DIR__ . '/../vendor/autoload.php';

class Service
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}

/**
 * @IsPublic(true)
 */
class Factory implements AutoFactory
{
    /**
     * @Id("service")
     */
    public static function createService(): Service
    {
        return new Service(0);
    }

    /**
     * @Id("service1")
     * @Tag("services.odd")
     */
    public static function createService1(): Service
    {
        return new Service(1);
    }

    /**
     * @Id("service2")
     * @Tag("services.even")
     */
    public static function createService2(): Service
    {
        return new Service(2);
    }

    /**
     * @Id("service3")
     * @Tag("services.odd")
     */
    public static function createService3(): Service
    {
        return new Service(3);
    }

    /**
     * @Id("service4")
     * @Tag("services.even")
     */
    public static function createService4(): Service
    {
        return new Service(4);
    }
}

$parser    = new AutoFactoryParser();
$container = new ContainerBuilder();

$serviceDefinitions = $parser->parse(Factory::class);

foreach ($serviceDefinitions as $serviceDefinition) {
    ContainerUtils::register($serviceDefinition, $container);
}

$compiler = new Compiler();
$compiler->compile($container);

foreach ($container->findTaggedServiceIds('services.odd') as $serviceId => $attributes) {
    echo " Odd: {$container->get($serviceId)->getId()}\n";
}

foreach ($container->findTaggedServiceIds('services.even') as $serviceId => $attributes) {
    echo "Even: {$container->get($serviceId)->getId()}\n";
}


