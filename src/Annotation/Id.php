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

    /**
     * @var string
     */
    private $id;

    /**
     * @param string[] $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $this->id = $values['value'] ?? null;

        if (count($values) > 1 || $this->id === null || !is_string($this->id)) {
            throw new AnnotationException('Annotation expects exactly one unnamed string value. Annotation usage hint: ' . self::USAGE_HINT);
        }
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
