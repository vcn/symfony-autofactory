<?php

use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vcn\Symfony\AutoFactory\Annotation\Alias;
use Vcn\Symfony\AutoFactory\Annotation\Id;
use Vcn\Symfony\AutoFactory\Annotation\IsPublic;
use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\AutoFactoryParser;
use Vcn\Symfony\AutoFactory\ContainerUtils;

require __DIR__ . '/../vendor/autoload.php';

class Service {
    public function sayHello(): void
    {
        echo "Hello, world!" . PHP_EOL;
    }
}

class Factory implements AutoFactory
{
    /**
     * @IsPublic(true)
     * @Id("service")
     * @Alias(id="service.alias", public=true)
     *
     * @return Service
     */
    public static function createService(): Service
    {
        return new Service();
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

/**
 * @var Service $service1
 * @var Service $service2
 */
$service1 = $container->get('service');
$service2 = $container->get('service.alias');

$service1->sayHello();
$service2->sayHello();

$servicesEqual = ($service1 === $service2) ? 'true' : 'false';
echo "\$service1 === \$service2: {$servicesEqual}";
