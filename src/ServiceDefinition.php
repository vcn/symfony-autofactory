<?php

namespace Vcn\Symfony\AutoFactory;

use Symfony\Component\DependencyInjection\Reference;

class ServiceDefinition
{
    /**
     * @var string
     */
    private $factoryClass;

    /**
     * @var string
     */
    private $factoryMethod;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $serviceClass;

    /**
     * @var bool
     */
    private $public;

    /**
     * @var bool
     */
    private $autoconfigured;

    /**
     * @var bool
     */
    private $autowired;

    /**
     * @var array
     */
    private $bindings;

    /**
     * @var array
     */
    private $aliases;

    /**
     * @var array
     */
    private $tags;

    /**
     * @param string      $factoryClass
     * @param string      $factoryMethod
     * @param string      $serviceId
     * @param string      $serviceClass
     * @param bool        $public
     * @param bool        $autoconfigured
     * @param bool        $autowired
     * @param Reference[] $bindings
     * @param array       $aliases
     * @param array       $tags
     */
    public function __construct(
        string $factoryClass,
        string $factoryMethod,
        string $serviceId,
        string $serviceClass,
        bool $public,
        bool $autoconfigured,
        bool $autowired,
        array $bindings,
        array $aliases,
        array $tags
    ) {
        $this->factoryClass   = $factoryClass;
        $this->factoryMethod  = $factoryMethod;
        $this->serviceId      = $serviceId;
        $this->serviceClass   = $serviceClass;
        $this->public         = $public;
        $this->autoconfigured = $autoconfigured;
        $this->autowired      = $autowired;
        $this->bindings       = $bindings;
        $this->aliases        = $aliases;
        $this->tags           = $tags;
    }

    /**
     * @return string
     */
    public function getFactoryClass(): string
    {
        return $this->factoryClass;
    }

    /**
     * @return string
     */
    public function getFactoryMethod(): string
    {
        return $this->factoryMethod;
    }

    /**
     * @return string
     */
    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    /**
     * @return string
     */
    public function getServiceClass(): string
    {
        return $this->serviceClass;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @return bool
     */
    public function isAutoconfigured(): bool
    {
        return $this->autoconfigured;
    }

    /**
     * @return bool
     */
    public function isAutowired(): bool
    {
        return $this->autowired;
    }

    /**
     * @return Reference[]
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
