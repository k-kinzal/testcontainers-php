<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use LogicException;
use Testcontainers\Utility\Ensure;
use Testcontainers\Utility\Stringable;

/**
 * MySQLDSN provides a way to define a MySQL Data Source Name (DSN).
 * It allows setting the host, port, database name, and character set for the DSN.
 *
 * @see https://www.php.net/manual/en/ref.pdo-mysql.connection.php
 * @api
 */
class MySQLDSN implements DSN, Stringable
{
    use Ensure;

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

    /**
     * Set the host used by the connection.
     *
     * @param string $host the host
     * @psalm-param mixed $host
     *
     * @return static
     */
    public function withHost($host)
    {
        self::ensureString($host);

        $this->host = $host;

        return $this;
    }

    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the port used by the connection.
     *
     * @param int|string $port the port
     * @psalm-param mixed $port
     *
     * @return static
     */
    public function withPort($port)
    {
        self::ensureIntOrDigitStringRange($port, 1, 65535);

        $port = (int) $port;

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
     * @psalm-param mixed $dbname
     *
     * @return $this
     */
    public function withDbname($dbname)
    {
        self::ensureString($dbname);

        $this->dbname = $dbname;

        return $this;
    }

    /**
     * Set the character set for the DSN.
     *
     * @param string $charset the character set to use
     * @psalm-param mixed $charset
     *
     * @return $this
     */
    public function withCharset($charset)
    {
        self::ensureString($charset);

        $this->charset = $charset;

        return $this;
    }

    public function toString()
    {
        if ($this->host === null) {
            throw new LogicException('Host is required');
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

    public function requiresHostPort()
    {
        return true;
    }
}
