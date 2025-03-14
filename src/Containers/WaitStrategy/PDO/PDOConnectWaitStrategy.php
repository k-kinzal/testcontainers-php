<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use LogicException;
use PDO;
use PDOException;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;

/**
 * PDOConnectWaitStrategy ensures that a PDO connection is established before proceeding.
 * This strategy continuously checks the readiness of the PDO connection until it is successfully established or a timeout occurs.
 *
 * Note: Do not use this strategy in PHP 5.6 as it may cause a Fatal error due to memory leaks.
 */
class PDOConnectWaitStrategy implements WaitStrategy
{
    /**
     * The DSN (Data Source Name) for the PDO connection.
     *
     * This property holds the DSN instance, which contains the connection details
     * such as host, port, and database name. It can be null if not set.
     *
     * @var DSN|null
     */
    private $dsn;

    /**
     * The username for the PDO connection.
     *
     * This property holds the username used to authenticate the PDO connection.
     *
     * @var string|null
     */
    private $username;

    /**
     * The password for the PDO connection.
     *
     * This property holds the password used to authenticate the PDO connection.
     *
     * @var string|null
     */
    private $password;

    /**
     * Timeout duration in seconds for waiting until the container instance is ready.
     *
     * @var int
     */
    private $timeout = 30;

    /**
     * The interval duration between each retry attempt in microseconds.
     *
     * This property defines how long the wait strategy should pause between each retry
     * when checking if the container is ready. The interval is specified in microseconds.
     *
     * @var int The retry interval in microseconds.
     */
    private $retryInterval = 100;

    /**
     * @inheritDoc
     *
     * @throws WaitingTimeoutException If the timeout duration is exceeded.
     */
    public function waitUntilReady($instance)
    {
        if ($this->dsn === null) {
            throw new LogicException('The DSN for the PDO connection is not set');
        }

        $dsn = clone $this->dsn;
        if ($dsn->getHost() === null) {
            $host = str_replace('localhost', '127.0.0.1', $instance->getHost());
            $dsn = $dsn->withHost($host);
        }
        if ($dsn->getPort() === null) {
            $ports = $instance->getExposedPorts();
            if (count($ports) !== 1) {
                throw new LogicException('PDOConnectWaitStrategy requires exactly one exposed port: ' . count($ports) . ' exposed');
            }
            $port = $instance->getMappedPort($ports[0]);
            $dsn = $dsn->withPort($port);
        }

        $now = time();
        $ex = null;
        while (1) {
            if (time() - $now > $this->timeout) {
                $message = $dsn->toString() . ': ' . $ex->getMessage();
                throw new WaitingTimeoutException($this->timeout, $message, 0, $ex);
            }
            try {
                $pdo = new PDO($dsn->toString(), $this->username, $this->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 1,
                ]);
                $pdo->query('SELECT 1');
                $pdo = null;

                break;
            } catch (PDOException $e) {
                $ex = $e;
            }
            usleep($this->retryInterval);
        }
    }

    /**
     * Specify the DSN (Data Source Name) for the PDO connection.
     *
     * This method sets the DSN for the PDO connection, allowing you to define
     * the connection details such as host, port, and database name.
     *
     * @param DSN $dsn The DSN instance containing connection details.
     * @return $this
     */
    public function withDsn(DSN $dsn)
    {
        $this->dsn = $dsn;

        return $this;
    }

    /**
     * Set the username for the PDO connection.
     *
     * This method sets the username used to authenticate the PDO connection.
     *
     * @param string $username The username for the PDO connection.
     * @return $this
     */
    public function withUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Specify the password for the PDO connection.
     *
     * This method sets the password used to authenticate the PDO connection.
     *
     * @param string $password The password for the PDO connection.
     * @return $this
     */
    public function withPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the timeout duration for waiting until the container instance is ready.
     *
     * This method allows you to specify how long (in seconds) the wait strategy should wait
     * for the container to be ready before timing out.
     *
     * @param int $timeout The timeout duration in seconds.
     * @return $this
     */
    public function withTimeoutSeconds($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set the interval duration between each retry in microseconds.
     *
     * This method allows you to specify the interval duration between each retry
     * when waiting for the container to be ready. The interval is defined in microseconds.
     *
     * @param int $interval The interval duration in microseconds.
     * @return $this
     */
    public function withRetryInterval($interval)
    {
        $this->retryInterval = $interval;
        return $this;
    }
}
