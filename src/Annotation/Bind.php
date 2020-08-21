<?php

namespace Vcn\Symfony\AutoFactory\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Bind
{
    private const USAGE_HINT = '@Bind(arg="$fooService", id="fooservice.default") or @Bind(arg="$fooService", param="fooservice.default")';

    private string  $arg;
    private ?string $id;
    private ?string $param;

    /**
     * @param string[] $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        if (
            count($values) !== 2
            || !isset($values['arg'])
            || (!isset($values['id']) && !isset($values['param']))
        ) {
            throw AnnotationException::semanticalError(
                'Invalid annotation usage. Annotation usage hint: ' . self::USAGE_HINT
            );
        }

        $this->arg   = $values['arg'];
        $this->id    = $values['id'] ?? null;
        $this->param = $values['param'] ?? null;
    }

    public function getArg(): string
    {
        return $this->arg;
    }

    public function hasId(): bool
    {
        return $this->id !== null;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function hasParam(): bool
    {
        return $this->param !== null;
    }

    public function getParam(): ?string
    {
        return $this->param;
    }
}
