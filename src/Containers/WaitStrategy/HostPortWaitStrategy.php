<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * HostPortWaitStrategy waits until the specified ports on the container instance are available.
 *
 * This strategy continuously checks the readiness of the specified ports on the container instance until they are available or a timeout occurs.
 */
class HostPortWaitStrategy implements WaitStrategy
{
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
     * {@inheritdoc}
     */
    public function waitUntilReady($instance)
    {
        $now = time();

        $host = $instance->getHost();
        $mappedPorts = [];
        foreach ($instance->getExposedPorts() as $port) {
            $mappedPorts[] = $instance->getMappedPort($port);
        }

        $ports = array_merge($this->ports, $mappedPorts);
        foreach ($ports as $port) {
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

    /**
     * Sets the ports to be checked for readiness.
     *
     * @param int[] $ports An array of port numbers.
     * @return $this The current instance for method chaining.
     */
    public function withPorts($ports)
    {
        $this->ports = $ports;

        return $this;
    }

    /**
    * Sets the timeout duration for waiting until the container instance is ready.
    *
    * @param int $seconds The number of seconds to wait before timing out.
    * @return $this The current instance for method chaining.
    */
    public function withTimeoutSeconds($seconds)
    {
        $this->timeout = $seconds;

        return $this;
    }
}
