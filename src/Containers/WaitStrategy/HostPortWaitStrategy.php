<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Utility\WithLogger;

/**
 * HostPortWaitStrategy waits until the specified ports on the container instance are available.
 *
 * This strategy continuously checks the readiness of the specified ports on the container instance until they are available or a timeout occurs.
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
     * @param PortProbeTcp $probe
     */
    public function __construct($probe = null)
    {
        $this->probe = $probe ?: new PortProbeTcp();
    }

    /**
     * Sets the ports to be checked for readiness.
     *
     * @param int[] $ports an array of port numbers
     *
     * @return $this the current instance for method chaining
     */
    public function withPorts($ports)
    {
        $this->ports = $ports;

        return $this;
    }

    /**
     * Sets the timeout duration for waiting until the container instance is ready.
     *
     * @param int $seconds the number of seconds to wait before timing out
     *
     * @return $this the current instance for method chaining
     */
    public function withTimeoutSeconds($seconds)
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Waits until the container instance is ready.
     *
     * @param ContainerInstance $instance the container instance to check
     */
    public function waitUntilReady($instance)
    {
        $now = time();

        $host = $instance->getHost();
        $mappedPorts = [];
        foreach ($instance->getExposedPorts() as $port) {
            $p = $instance->getMappedPort($port);
            if (null === $p) {
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
                if ($this->probe->available($host, $port)) {
                    break;
                }
                usleep(0);
            }
        }
    }
}
