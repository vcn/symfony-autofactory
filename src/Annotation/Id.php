<?php

namespace Vcn\Symfony\AutoFactory\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Id
{
    private const USAGE_HINT = '@Id("service.id")';

    private string $id;

    /**
     * @param string[] $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $id = $values['value'] ?? null;

        if (count($values) > 1 || $id === null || !is_string($id)) {
            throw new AnnotationException('Annotation expects exactly one unnamed string value. Annotation usage hint: ' . self::USAGE_HINT);
        }

        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
