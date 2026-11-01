<?php

namespace Testcontainers\Exceptions;

use Exception;
use RuntimeException;

/**
 * One or more containers could not be stopped.
 *
 * @api
 */
class ContainerStopException extends RuntimeException
{
    /**
     * The failures collected for each container that could not be stopped.
     *
     * @var array<string, Exception>
     */
    private $errors;

    /**
     * Collect failures encountered while stopping containers.
     *
     * @param array<string, Exception> $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct(
            sprintf('Failed to stop %d container(s)', count($errors)),
            0,
            empty($errors) ? null : reset($errors)
        );
    }

    /**
     * Get the failures collected while stopping containers.
     *
     * @return array<string, Exception>
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
