<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use Testcontainers\Utility\Stringable;

/**
 * SQLiteDSN is a class that implements the DSN interface for SQLite database connections.
 *
 * This class provides methods to set and retrieve the host and port for the SQLite database connection.
 * It also provides a method to convert the DSN to a string representation.
 */
class SQLiteDSN implements DSN, Stringable
{
    public function __toString()
    {
        return $this->toString();
    }

    public function withHost($host)
    {
        return $this;
    }

    public function getHost()
    {
        return null;
    }

    public function withPort($port)
    {
        return $this;
    }

    public function getPort()
    {
        return null;
    }

    public function toString()
    {
        // TODO: support file path
        return 'sqlite::memory:';
    }
}
