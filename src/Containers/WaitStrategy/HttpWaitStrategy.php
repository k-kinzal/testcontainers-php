<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * HttpWaitStrategy waits until a specified HTTP endpoint is reachable.
 *
 * This strategy continuously checks the readiness of the specified HTTP endpoint
 * until it is reachable or a timeout occurs.
 */
class HttpWaitStrategy implements WaitStrategy
{
    /**
     * The HTTP probe used to check the availability of the endpoint.
     *
     * @var HttpProbe
     */
    private $probe;

    /**
     * The endpoint URL to be checked for readiness.
     *
     * This can be a full URL or null. If null, the endpoint will be constructed
     * using the schema, host, port, and path properties.
     *
     * @var string|null
     */
    private $endpoint;

    /**
     * The schema (protocol) to be used in the endpoint URL (e.g., 'http' or 'https').
     *
     * If not set, the default value 'http' will be used.
     *
     * @var string|null
     */
    private $schema;

    /**
     * The host to be used in the endpoint URL.
     *
     * If not set, the default value 'localhost' will be used.
     *
     * @var string|null
     */
    private $host;

    /**
     * The path to be used in the endpoint URL.
     *
     * If not set, the default value '/' will be used.
     *
     * @var string|null The path to be used in the endpoint URL, or null if not set.
     */
    private $path;

    /**
    * The port to be used in the endpoint URL.
    *
    * If not set, the port will be determined dynamically based on the container's exposed ports.
    *
    * @var int|null The port number, or null if not set.
    */
    private $port;

    /**
     * The expected HTTP response code for the endpoint.
     *
     * @var int
     */
    private $expectedResponseCode = 200;

    /**
     * The timeout duration in seconds for waiting until the container instance is ready.
     *
     * This value determines how long the strategy will wait for the container to become ready
     * before throwing a timeout exception. The default is 30 seconds.
     *
     * @var int The timeout duration in seconds.
     */
    private $timeout = 30;

    /**
     * @param null|HttpProbe $probe
     */
    public function __construct($probe = null)
    {
        $this->probe = $probe ?: new HttpProbeGetHeaders();
    }

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
            $port = $this->port ?: $instance->getMappedPort($instance->getExposedPorts()[0]);
            $path = $this->path ?: '/';
            $endpoint = sprintf('%s://%s:%s%s', $schema, $host, $port, $path);
        }

        while (1) {
            if (time() - $now > $this->timeout) {
                throw new WaitingTimeoutException($this->timeout);
            }
            if ($this->probe->available($endpoint, $this->expectedResponseCode)) {
                break;
            }
            usleep(100);
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
     * Sets the expected HTTP response code for the endpoint.
     *
     * @param int $responseCode The expected HTTP response code.
     * @return $this
     */
    public function withExpectedResponseCode($responseCode)
    {
        $this->expectedResponseCode = $responseCode;

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
