<?php

namespace Vcn\Symfony\AutoFactory;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class AutoFactoryParser
{
    /**
     * @var bool
     */
    private static $annotationLoaderRegistered = false;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct()
    {
        if (!self::$annotationLoaderRegistered) {
            // This (for now) is the correct way to do this: https://github.com/doctrine/annotations/issues/182
            AnnotationRegistry::registerLoader('class_exists');
            self::$annotationLoaderRegistered = true;
        }

        try {
            $this->annotationReader = new AnnotationReader();
        } catch (Exception $e) {
            throw new RuntimeException("Could not create AnnotationReader", 0, $e);
        }
    }

    /**
     * @param string $autoFactoryClass
     *
     * @return ServiceDefinition[]
     *
     * @throws AnnotationException
     */
    public function parse(string $autoFactoryClass): array
    {
        try {
            $class = new ReflectionClass($autoFactoryClass);
        } catch (\ReflectionException $e) {

        }

        $serviceDefinitions = [];

        [$classPublic, $classAutoconfigured, $classAutowired] = $this->parseClassAnnotations($class);

        $defaultPublic         = $classPublic ?? false;
        $defaultAutoconfigured = $classAutoconfigured ?? true;
        $defaultAutowired      = $classAutowired ?? true;

        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC);

        foreach ($methods as $method) {
            $factoryName = "{$class->getName()}::{$method->getName()}";
            $returnType  = $method->getReturnType();

            if ($returnType === null) {
                throw new AutoFactoryException("Return type is required for factory {$factoryName}");
            }

            $serviceClass = $returnType->getName();

            if (!class_exists($serviceClass, true) && !interface_exists($serviceClass, true)) {
                throw new AutoFactoryException("The return type of {$factoryName} is not a valid class");
            }

            $annotations   = $this->annotationReader->getMethodAnnotations($method);
            $annotationMap = $this->createAnnotationMap($annotations);

            /**
             * @var Annotation\Id[]            $idAnnotations
             * @var Annotation\IsPublic[]      $isPublicAnnotations
             * @var Annotation\Autoconfigure[] $autoconfigureAnnotations
             * @var Annotation\Autowire[]      $autowireAnnotations
             * @var Annotation\Alias[]         $aliasAnnotations
             * @var Annotation\Bind[]          $bindAnnotations
             * @var Annotation\Tag[]           $tagAnnotations
             */
            $isPublicAnnotations      = $annotationMap[Annotation\IsPublic::class] ?? [];
            $autoconfigureAnnotations = $annotationMap[Annotation\Autoconfigure::class] ?? [];
            $autowireAnnotations      = $annotationMap[Annotation\Autowire::class] ?? [];
            $idAnnotations            = $annotationMap[Annotation\Id::class] ?? [];
            $aliasAnnotations         = $annotationMap[Annotation\Alias::class] ?? [];
            $bindAnnotations          = $annotationMap[Annotation\Bind::class] ?? [];
            $tagAnnotations           = $annotationMap[Annotation\Tag::class] ?? [];

            if (count($idAnnotations) > 1) {
                throw AnnotationException::semanticalError("0 or 1 Id annotations are allowed, found more in {$factoryName}");
            }

            if (count($isPublicAnnotations) > 1) {
                throw AnnotationException::semanticalError("0 or 1 IsPublic annotations are allowed, found more in {$factoryName}");
            }

            if (count($autoconfigureAnnotations) > 1) {
                throw AnnotationException::semanticalError("0 or 1 IsAutoconfigured annotations are allowed, found more in {$factoryName}");
            }

            if (count($autowireAnnotations) > 1) {
                throw AnnotationException::semanticalError("0 or 1 IsAutowired annotations are allowed, found more in {$factoryName}");
            }

            $public = isset($isPublicAnnotations[0])
                ? $isPublicAnnotations[0]->getValue()
                : null;

            $autoconfigured = isset($autoconfigureAnnotations[0])
                ? $autoconfigureAnnotations[0]->getValue()
                : null;

            $autowired = isset($autowireAnnotations[0])
                ? $autowireAnnotations[0]->getValue()
                : null;

            $serviceId = isset($idAnnotations[0])
                ? $idAnnotations[0]->getId()
                : null;

            $bindings = [];
            foreach ($bindAnnotations as $bindAnnotation) {
                if ($bindAnnotation->hasId()) {
                    $bindings[$bindAnnotation->getArg()] = new Reference($bindAnnotation->getId());
                } else {
                    $bindings[$bindAnnotation->getArg()] = new Parameter($bindAnnotation->getParam());
                }
            }

            $aliases = [];
            foreach ($aliasAnnotations as $aliasAnnotation) {
                $aliases[$aliasAnnotation->getId()] = $aliasAnnotation->isPublic();
            }

            $tags = [];
            foreach ($tagAnnotations as $tagAnnotation) {
                $name  = $tagAnnotation->getName();
                $attrs = $tagAnnotation->getAttributes();

                if (isset($tags[$name])) {
                    $tags[$name][] = $attrs;
                } else {
                    $tags[$tagAnnotation->getName()] = [$tagAnnotation->getAttributes()];
                }
            }

            $serviceId = $serviceId ?? $serviceClass;

            $serviceDefinitions[] = new ServiceDefinition(
                $class->getName(),
                $method->getName(),
                $serviceId,
                $serviceClass,
                $public ?? $defaultPublic,
                $autoconfigured ?? $defaultAutoconfigured,
                $autowired ?? $defaultAutowired,
                $bindings,
                $aliases,
                $tags
            );
        }

        return $serviceDefinitions;
    }

    /**
     * @param ReflectionClass $class
     *
     * @return array [?bool $public, ?bool $autoconfigured, ?bool $autowired]
     *
     * @throws AnnotationException
     */
    private function parseClassAnnotations(ReflectionClass $class): array
    {
        $annotations  = $this->annotationReader->getClassAnnotations($class);
        $factoryClass = $class->getName();

        $annotationMap = $this->createAnnotationMap($annotations);

        /**
         * @var Annotation\Id[]            $idAnnotations
         * @var Annotation\IsPublic[]      $isPublicAnnotations
         * @var Annotation\Autoconfigure[] $autoconfigureAnnotations
         * @var Annotation\Autowire[]      $autowireAnnotations
         * @var Annotation\Alias[]         $aliasAnnotations
         * @var Annotation\Bind[]          $bindAnnotations
         */
        $isPublicAnnotations      = $annotationMap[Annotation\IsPublic::class] ?? [];
        $autoconfigureAnnotations = $annotationMap[Annotation\Autoconfigure::class] ?? [];
        $autowireAnnotations      = $annotationMap[Annotation\Autowire::class] ?? [];

        if (count($isPublicAnnotations) > 1) {
            throw AnnotationException::semanticalError("0 or 1 IsPublic annotations are allowed, found more in {$factoryClass}");
        }

        if (count($autoconfigureAnnotations) > 1) {
            throw AnnotationException::semanticalError("0 or 1 IsAutoconfigured annotations are allowed, found more in {$factoryClass}");
        }

        if (count($autowireAnnotations) > 1) {
            throw AnnotationException::semanticalError("0 or 1 IsAutowired annotations are allowed, found more in {$factoryClass}");
        }

        $public = isset($isPublicAnnotations[0])
            ? $isPublicAnnotations[0]->getValue()
            : null;

        $autoconfigured = isset($autoconfigureAnnotations[0])
            ? $autoconfigureAnnotations[0]->getValue()
            : null;

        $autowired = isset($autowireAnnotations[0])
            ? $autowireAnnotations[0]->getValue()
            : null;

        return [$public, $autoconfigured, $autowired];
    }

    /**
     * @param array $annotations
     *
     * @return array
     */
    private function createAnnotationMap(array $annotations): array
    {
        $map = [];

        foreach ($annotations as $annotation) {
            $annotationClass = get_class($annotation) ?: null;

            if ($annotationClass === null) {
                continue;
            }

            if (!isset($map[$annotationClass])) {
                $map[$annotationClass] = [$annotation];
            } else {
                $map[$annotationClass][] = $annotation;
            }
        }

        return $map;
    }
}
