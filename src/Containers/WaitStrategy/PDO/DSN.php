<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

/**
 * DSN interface for defining Data Source Name components.
 */
interface DSN
{
    /**
     * Set the host for the DSN.
     *
     * @param string $host The hostname to set.
     * @return $this
     */
    public function withHost($host);

    /**
     * Set the port for the DSN.
     *
     * @param int $port The port to set.
     * @return $this
     */
    public function withPort($port);

    /**
     * Convert the DSN to a string representation.
     *
     * @return string The string representation of the DSN.
     */
    public function toString();

    public function __toString();
}
