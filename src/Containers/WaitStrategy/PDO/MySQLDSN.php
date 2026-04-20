<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use Testcontainers\Utility\Stringable;

use function Testcontainers\ensure;

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
        ensure(is_string($host), '$host must be string');

        $this->host = $host;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritDoc}
     */
    public function withPort($port)
    {
        ensure(is_int($port), '$port must be int');

        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
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
        ensure(is_string($dbname), '$dbname must be string');

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
        ensure(is_string($charset), '$charset must be string');

        $this->charset = $charset;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        if ($this->host === null) {
            throw new \LogicException('Host is required');
        }
        $dsn = sprintf('mysql:host=%s;', $this->host);
        if ($this->port !== null) {
            $dsn .= 'port='.$this->port.';';
        }
        if ($this->dbname !== null) {
            $dsn .= 'dbname='.$this->dbname.';';
        }
        if ($this->charset !== null) {
            $dsn .= 'charset='.$this->charset.';';
        }

        return $dsn;
    }

    /**
     * {@inheritDoc}
     */
    public function requiresHostPort()
    {
        return true;
    }
}
