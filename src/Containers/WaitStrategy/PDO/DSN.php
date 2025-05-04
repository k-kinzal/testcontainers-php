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
     * Get the host for the DSN.
     *
     * @return string|null
     */
    public function getHost();

    /**
     * Set the port for the DSN.
     *
     * @param int $port The port to set.
     * @return $this
     */
    public function withPort($port);

    /**
     * Get the port for the DSN.
     *
     * @return int|null
     */
    public function getPort();

    /**
     * Convert the DSN to a string representation.
     *
     * @return string The string representation of the DSN.
     */
    public function toString();
}
