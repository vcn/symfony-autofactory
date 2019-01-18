<?php

namespace Vcn\Symfony\AutoFactory\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
class IsPublic
{
    private const USAGE_HINT = '@IsPublic(true|false)';

    /**
     * @var bool
     */
    private $value;

    /**
     * @param array $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $this->value = $values['value'] ?? null;

        if (count($values) > 1 || $this->value === null || !is_bool($this->value)) {
            throw new AnnotationException('Annotation expects exactly one unnamed bool value. Annotation usage hint: ' . self::USAGE_HINT);
        }
    }

    /**
     * @return bool
     */
    public function getValue(): bool
    {
        return $this->value;
    }
}
