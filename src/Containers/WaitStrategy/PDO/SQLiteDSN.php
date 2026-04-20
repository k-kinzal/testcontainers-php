<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use Testcontainers\Utility\Stringable;

use function Testcontainers\ensure;

/**
 * SQLiteDSN is a class that implements the DSN interface for SQLite database connections.
 *
 * This class provides methods to set and retrieve the host and port for the SQLite database connection.
 * It also provides a method to convert the DSN to a string representation.
 */
class SQLiteDSN implements DSN, Stringable
{
    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * {@inheritDoc}
     */
    public function withHost($host)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($host), '$host must be string');

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_int($port), '$port must be int');

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        // TODO: support file path
        return 'sqlite::memory:';
    }

    /**
     * {@inheritDoc}
     */
    public function requiresHostPort()
    {
        return false;
    }
}
