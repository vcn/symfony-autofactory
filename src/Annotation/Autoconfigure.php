<?php

namespace Vcn\Symfony\AutoFactory\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
class Autoconfigure
{
    private const USAGE_HINT = '@IsAutoconfigured(true|false)';

    private bool $value;

    /**
     * @param array $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $value = $values['value'] ?? null;

        if (count($values) > 1 || $value === null || !is_bool($value)) {
            throw new AnnotationException('Annotation expects exactly one unnamed bool value. Annotation usage hint: ' . self::USAGE_HINT);
        }

        $this->value = $value;
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
