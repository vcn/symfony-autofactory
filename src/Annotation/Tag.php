<?php

namespace Vcn\Symfony\AutoFactory\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Tag
{
    private const USAGE_HINT = '@Tag("some.tag"[, attribute="foobar"[, anotherAttribute="barbaz"[,...]])';

    private string $name;
    private array  $attributes;

    /**
     * @param string[] $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $name = $values['value'] ?? null;

        if ($name === null) {
            throw new AnnotationException('Annotation requires name-parameter. Annotation usage hint: ' . self::USAGE_HINT);
        }

        $this->name = $name;

        unset($values['value']);
        $this->attributes = $values;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
