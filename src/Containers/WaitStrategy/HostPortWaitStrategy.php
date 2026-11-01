<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Logger\WithLogger;

/**
 * HostPortWaitStrategy waits until the specified ports on the container instance are available.
 *
 * This strategy continuously checks the readiness of the specified ports on the container instance until they are available or a timeout occurs.
 * @api
 */
class HostPortWaitStrategy implements WaitStrategy
{
    use WithLogger;

    /**
     * The port probe used to check the availability of ports.
     *
     * @var PortProbe
     */
    private $probe;

    /**
     * The ports to be checked for readiness.
     *
     * @var int[]
     */
    private $ports = [];

    /**
     * Timeout duration in seconds for waiting until the container instance is ready.
     * Default is 30 seconds.
     *
     * @var int
     */
    private $timeout = 30;

    /**
     * The interval duration between each retry attempt in microseconds.
     *
     * @var int the retry interval in microseconds
     */
    private $retryInterval = 100000;

    /**
     * @param PortProbeTcp $probe
     * @psalm-param mixed $probe
     */
    public function __construct($probe = null)
    {
        if ($probe !== null) {
            self::ensureInstanceOf($probe, PortProbe::class);

        }

        $this->probe = $probe ?: new PortProbeTcp();
    }

    /**
     * Sets the ports to be checked for readiness.
     *
     * @param int[] $ports an array of port numbers
     * @psalm-param mixed $ports
     *
     * @return $this the current instance for method chaining
     */
    public function withPorts($ports)
    {
        self::ensureArray($ports);

        $normalizedPorts = [];
        foreach (array_keys($ports) as $key) {
            self::ensureIntOrDigitString($ports[$key]);

            if ($ports[$key] < 1 || $ports[$key] > 65535) {
                throw new \InvalidArgumentException('Port must be between 1 and 65535');
            }
            $normalizedPorts[] = (int) $ports[$key];
        }
        $ports = $normalizedPorts;

        $this->ports = $ports;

        return $this;
    }

    /**
     * Sets the timeout duration for waiting until the container instance is ready.
     *
     * @param int $seconds the number of seconds to wait before timing out
     * @psalm-param mixed $seconds
     *
     * @return $this the current instance for method chaining
     */
    public function withTimeoutSeconds($seconds)
    {
        self::ensureIntRange($seconds, 0);

        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Sets the interval duration between each retry attempt.
     *
     * @param int $interval the interval duration in microseconds
     * @psalm-param mixed $interval
     *
     * @return $this the current instance for method chaining
     */
    public function withRetryInterval($interval)
    {
        self::ensureIntRange($interval, 0);

        $this->retryInterval = $interval;

        return $this;
    }

    /**
     * Waits until the container instance is ready.
     *
     * @param ContainerInstance $instance the container instance to check
     * @psalm-param mixed $instance
     * @return void
     */
    public function waitUntilReady($instance)
    {
        self::ensureInstanceOf($instance, ContainerInstance::class);

        $now = time();

        $host = $instance->getHost();
        $mappedPorts = [];
        foreach ($instance->getExposedPorts() as $port) {
            $p = $instance->getMappedPort($port);
            if ($p === null) {
                continue;
            }
            $mappedPorts[] = $p;
        }

        $ports = array_merge($this->ports, $mappedPorts);
        foreach ($ports as $port) {
            $this->logger()->debug(sprintf(
                'Waiting for port available: host=%s:%d, timeout=%d',
                $host,
                $port,
                $this->timeout
            ));
            while (1) {
                if (time() - $now > $this->timeout) {
                    throw new WaitingTimeoutException($this->timeout);
                }
                if ($instance->isStopped()) {
                    throw new ContainerStoppedException('Container stopped while waiting for port availability');
                }
                if ($this->probe->available($host, $port)) {
                    break;
                }
                usleep($this->retryInterval);
            }
        }
    }
}
