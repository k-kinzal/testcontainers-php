<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use LogicException;
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
     * @var string|null
     */
    private $host;

    /**
     * The port number for the DSN.
     *
     * @var int|null
     */
    private $port;

    /**
     * The name of the database.
     *
     * @var string|null
     */
    private $dbname;


    /**
     * The character set to use for the DSN.
     *
     * @var string|null
     */
    private $charset;

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the database name for the DSN.
     *
     * @param string $dbname The name of the database.
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
     * @param string $charset The character set to use.
     * @return $this
     */
    public function withCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        if ($this->host === null) {
            throw new LogicException('Host is required');
        }
        $dsn = sprintf('mysql:host=%s;', $this->host);
        if ($this->port !== null) {
            $dsn .= 'port=' . $this->port . ';';
        }
        if ($this->dbname !== null) {
            $dsn .= 'dbname=' . $this->dbname . ';';
        }
        if ($this->charset !== null) {
            $dsn .= 'charset=' . $this->charset . ';';
        }
        return $dsn;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->toString();
    }
}
