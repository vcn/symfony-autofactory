<?php

namespace Vcn\Symfony\AutoFactory;

use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutoFactoryPass implements CompilerPassInterface
{
    private AutoFactoryParser $annotationParser;
    private string            $tag;

    public function __construct(string $tag)
    {
        $this->tag              = $tag;
        $this->annotationParser = new AutoFactoryParser();
    }

    /**
     * @inheritdoc
     *
     * @throws AnnotationException
     */
    public function process(ContainerBuilder $container)
    {
        $autoFactoryServiceIds = $container->findTaggedServiceIds($this->tag, true);

        foreach ($autoFactoryServiceIds as $autoFactoryClass => $attributes) {
            $serviceDefinitions = $this->annotationParser->parse($autoFactoryClass);

            foreach ($serviceDefinitions as $serviceDefinition) {
                ContainerUtils::register($serviceDefinition, $container);
            }
        }
    }
}
