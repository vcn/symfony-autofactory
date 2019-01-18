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

    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $attributes;

    /**
     * @param string[] $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $this->name = $values['value'] ?? null;

        if ($this->name === null) {
            throw new AnnotationException('Annotation requires name-parameter. Annotation usage hint: ' . self::USAGE_HINT);
        }

        unset($values['value']);
        $this->attributes = $values;
    }

    /**
     * @return string
     */
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
