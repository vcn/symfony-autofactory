<?php

use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vcn\Symfony\AutoFactory\Annotation\Autowire;
use Vcn\Symfony\AutoFactory\Annotation\IsPublic;
use Vcn\Symfony\AutoFactory\AutoFactory;
use Vcn\Symfony\AutoFactory\AutoFactoryParser;
use Vcn\Symfony\AutoFactory\ContainerUtils;

require __DIR__ . '/../vendor/autoload.php';

class InnerService
{
}

class OuterService
{
    /**
     * @var InnerService
     */
    private $innerService;

    /**
     * @param InnerService $innerService
     */
    public function __construct(InnerService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function serviceMe(): void
    {
        echo get_class($this->innerService) . PHP_EOL;
    }
}

class Factory implements AutoFactory
{
    public static function createInnerService(): InnerService
    {
        return new InnerService();
    }

    /**
     * @IsPublic(true)
     * @Autowire(true)
     *
     * @param InnerService $innerService
     *
     * @return OuterService
     */
    public static function createOuterService(InnerService $innerService): OuterService
    {
        return new OuterService($innerService);
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
 * @var OuterService $service2
 */
$service2 = $container->get(OuterService::class);
$service2->serviceMe();



