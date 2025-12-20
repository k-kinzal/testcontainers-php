<?php

namespace Testcontainers\Containers\WaitStrategy\PDO;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;
use Testcontainers\Utility\WithLogger;

/**
 * PDOConnectWaitStrategy ensures that a PDO connection is established before proceeding.
 * This strategy continuously checks the readiness of the PDO connection until it is successfully established or a timeout occurs.
 *
 * Note: Do not use this strategy in PHP 5.6 as it may cause a Fatal error due to memory leaks.
 */
class PDOConnectWaitStrategy implements WaitStrategy
{
    use WithLogger;

    /**
     * The DSN (Data Source Name) for the PDO connection.
     *
     * This property holds the DSN instance, which contains the connection details
     * such as host, port, and database name. It can be null if not set.
     *
     * @var null|DSN
     */
    private $dsn;

    /**
     * The username for the PDO connection.
     *
     * This property holds the username used to authenticate the PDO connection.
     *
     * @var null|string
     */
    private $username;

    /**
     * The password for the PDO connection.
     *
     * This property holds the password used to authenticate the PDO connection.
     *
     * @var null|string
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
     * @var int the retry interval in microseconds
     */
    private $retryInterval = 100;

    /**
     * Specify the DSN (Data Source Name) for the PDO connection.
     *
     * This method sets the DSN for the PDO connection, allowing you to define
     * the connection details such as host, port, and database name.
     *
     * @param DSN $dsn the DSN instance containing connection details
     *
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
     * @param string $username the username for the PDO connection
     *
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
     * @param string $password the password for the PDO connection
     *
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
     * @param int $timeout the timeout duration in seconds
     *
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
     * @param int $interval the interval duration in microseconds
     *
     * @return $this
     */
    public function withRetryInterval($interval)
    {
        $this->retryInterval = $interval;

        return $this;
    }

    /**
     * Waits until the container instance is ready.
     *
     * @param ContainerInstance $instance the container instance to check
     *
     * @throws WaitingTimeoutException if the timeout duration is exceeded
     */
    public function waitUntilReady($instance)
    {
        if (null === $this->dsn) {
            throw new \LogicException('The DSN for the PDO connection is not set');
        }

        $dsn = clone $this->dsn;

        if ($dsn->requiresHostPort()) {
            if (null === $dsn->getHost()) {
                $host = str_replace('localhost', '127.0.0.1', $instance->getHost());
                $dsn = $dsn->withHost($host);
            }
            if (null === $dsn->getPort()) {
                $ports = $instance->getExposedPorts();
                if (1 !== count($ports)) {
                    throw new \LogicException('PDOConnectWaitStrategy requires exactly one exposed port: '.count($ports).' exposed');
                }
                $port = $instance->getMappedPort($ports[0]);
                if (null === $port) {
                    throw new \LogicException('PDOConnectWaitStrategy requires exactly one mapped port');
                }
                $dsn = $dsn->withPort($port);
            }
        }

        $defaultSocketTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 1);

        $now = time();
        $ex = null;
        while (1) {
            if (time() - $now > $this->timeout) {
                ini_set('default_socket_timeout', $defaultSocketTimeout);
                if (null === $ex) {
                    $message = 'Timeout waiting for PDO connection';
                } else {
                    $message = $dsn->toString().': '.$ex->getMessage();
                }
                throw new WaitingTimeoutException($this->timeout, $message, 0, $ex);
            }
            if (!$instance->isRunning()) {
                throw new ContainerStoppedException('Container stopped while waiting for PDO connection');
            }

            try {
                $this->logger()->debug(sprintf(
                    'Trying to connect to PDO: dsn=%s, username=%s, password=%s',
                    $dsn->toString(),
                    $this->username,
                    $this->password
                ));
                $pdo = new \PDO($dsn->toString(), $this->username, $this->password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => 1,
                ]);
                $this->logger()->debug('Pinging PDO connection');
                $pdo->query('SELECT 1');
                $pdo = null;

                break;
            } catch (\PDOException $e) {
                $this->logger()->debug(sprintf('Unable to connect to PDO: %s', $e->getMessage()), [
                    'exception' => $e,
                ]);
                $ex = $e;
            }
            usleep($this->retryInterval);
        }

        ini_set('default_socket_timeout', $defaultSocketTimeout);
    }
}
