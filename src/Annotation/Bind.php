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
    private const USAGE_HINT = '@Bind(arg="$fooService", id="fooservice.default")';

    /**
     * @var string
     */
    private $arg;

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
        if (count($values) !== 2 || !isset($values['id']) || !isset($values['arg'])) {
            throw AnnotationException::semanticalError('Invalid annotation usage. Annotation usage hint: ' . self::USAGE_HINT);
        }

        $this->id  = $values['id'];
        $this->arg = $values['arg'];
    }

    /**
     * @return string
     */
    public function getArg(): string
    {
        return $this->arg;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
