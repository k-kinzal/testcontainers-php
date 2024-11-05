<?php

namespace Testcontainers\Containers\WaitStrategy;

use RuntimeException;

class HttpWaitStrategy implements WaitStrategy
{
    private $endpoint;

    private $schema;

    private $host;

    private $path;

    private $port;

    private $timeout = 30;

    /**
     * {@inheritdoc}
     */
    public function waitUntilReady($instance)
    {
        $now = time();
        $endpoint = $this->endpoint;
        if ($endpoint === null) {
            $schema = $this->schema ?: 'http';
            $host = $this->host ?: $instance->getHost();
            $port = $this->port ?: $instance->getMappingPort($instance->getExposedPorts()[0]);
            $path = $this->path ?: '/';
            $endpoint = sprintf('%s://%s:%s%s', $schema, $host, $port, $path);
        }

        while (1) {
            if (time() - $now > $this->timeout) {
                throw new RuntimeException('Timeout waiting for port to be ready');
            }
            if (@file_get_contents($endpoint) !== false) {
                break;
            }
            usleep(0);
        }
    }

    /**
     * Sets the endpoint to be checked for readiness.
     *
     * @param string $endpoint The endpoint to be checked.
     * @return $this
     */
    public function withEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Sets the schema to be used in the endpoint URL.
     *
     * @param string $schema The schema to be used.
     * @return $this
     */
    public function withSchema($schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Sets the host to be used in the endpoint URL.
     *
     * @param string $host The host to be used.
     * @return $this
     */
    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Sets the path to be used in the endpoint URL.
     *
     * @param string $path The path to be used.
     * @return $this
     */
    public function withPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Sets the port to be used in the endpoint URL.
     *
     * @param int $port The port to be used.
     * @return $this
     */
    public function withPort($port)
    {
        $this->port = $port;

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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'http';
    }
}
