<?php

namespace Testcontainers\Exceptions;

class ContainerStopException extends \RuntimeException
{
    /** @var array<string, \Exception> */
    private $errors;

    /**
     * @param array<string, \Exception> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct(
            sprintf('Failed to stop %d container(s)', count($errors)),
            0,
            reset($errors)
        );
    }

    /** @return array<string, \Exception> */
    public function getErrors()
    {
        return $this->errors;
    }
}
