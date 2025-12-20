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
     * @param string $host the hostname to set
     *
     * @return $this
     */
    public function withHost($host);

    /**
     * Get the host for the DSN.
     *
     * @return null|string
     */
    public function getHost();

    /**
     * Set the port for the DSN.
     *
     * @param int $port the port to set
     *
     * @return $this
     */
    public function withPort($port);

    /**
     * Get the port for the DSN.
     *
     * @return null|int
     */
    public function getPort();

    /**
     * Convert the DSN to a string representation.
     *
     * @return string the string representation of the DSN
     */
    public function toString();

    /**
     * Check if this DSN requires host and port resolution.
     *
     * @return bool true if the DSN requires host/port, false otherwise (e.g., SQLite :memory:)
     */
    public function requiresHostPort();
}
