<?php

namespace Vcn\Symfony\AutoFactory\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Alias
{
    private const USAGE_HINT = '@Alias(id="some.service.id",public=true|false)';

    /**
     * @var string
     */
    private $id;

    /**
     * @var null|bool
     */
    private $public;

    /**
     * @param string[] $values
     *
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $this->id     = $values['id'] ?? null;
        $this->public = $values['public'] ?? null;

        if (count($values) === 0 || $this->id === null || $this->public === null || !is_string($this->id) || !is_bool($this->public)) {
            throw AnnotationException::semanticalError('Invalid annotation usage. Annotation usage hint: ' . self::USAGE_HINT);
        }
    }

    /**
     * @return string[]
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }
}
