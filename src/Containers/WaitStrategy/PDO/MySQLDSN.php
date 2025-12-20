<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use Testcontainers\Utility\Stringable;

/**
 * MySQLDSN provides a way to define a MySQL Data Source Name (DSN).
 * It allows setting the host, port, database name, and character set for the DSN.
 *
 * @see https://www.php.net/manual/en/ref.pdo-mysql.connection.php
 */
class MySQLDSN implements DSN, Stringable
{
    /**
     * The hostname for the DSN.
     *
     * @var null|string
     */
    private $host;

    /**
     * The port number for the DSN.
     *
     * @var null|int
     */
    private $port;

    /**
     * The name of the database.
     *
     * @var null|string
     */
    private $dbname;

    /**
     * The character set to use for the DSN.
     *
     * @var null|string
     */
    private $charset;

    public function __toString()
    {
        return $this->toString();
    }

    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function withPort($port)
    {
        $this->port = $port;

        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the database name for the DSN.
     *
     * @param string $dbname the name of the database
     *
     * @return $this
     */
    public function withDbname($dbname)
    {
        $this->dbname = $dbname;

        return $this;
    }

    /**
     * Set the character set for the DSN.
     *
     * @param string $charset the character set to use
     *
     * @return $this
     */
    public function withCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    public function toString()
    {
        if (null === $this->host) {
            throw new \LogicException('Host is required');
        }
        $dsn = sprintf('mysql:host=%s;', $this->host);
        if (null !== $this->port) {
            $dsn .= 'port='.$this->port.';';
        }
        if (null !== $this->dbname) {
            $dsn .= 'dbname='.$this->dbname.';';
        }
        if (null !== $this->charset) {
            $dsn .= 'charset='.$this->charset.';';
        }

        return $dsn;
    }

    public function requiresHostPort()
    {
        return true;
    }
}
