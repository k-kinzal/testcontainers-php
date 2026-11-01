<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Logger\WithLogger;

/**
 * HttpWaitStrategy waits until a specified HTTP endpoint is reachable.
 *
 * This strategy continuously checks the readiness of the specified HTTP endpoint
 * until it is reachable or a timeout occurs.
 * @api
 */
class HttpWaitStrategy implements WaitStrategy
{
    use WithLogger;

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
     * @var null|string
     */
    private $endpoint;

    /**
     * The schema (protocol) to be used in the endpoint URL (e.g., 'http' or 'https').
     *
     * If not set, the default value 'http' will be used.
     *
     * @var null|string
     */
    private $schema;

    /**
     * The host to be used in the endpoint URL.
     *
     * If not set, the default value 'localhost' will be used.
     *
     * @var null|string
     */
    private $host;

    /**
     * The path to be used in the endpoint URL.
     *
     * If not set, the default value '/' will be used.
     *
     * @var null|string the path to be used in the endpoint URL, or null if not set
     */
    private $path;

    /**
     * The port to be used in the endpoint URL.
     *
     * If not set, the port will be determined dynamically based on the container's exposed ports.
     *
     * @var null|int the port number, or null if not set
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
     * @var int the timeout duration in seconds
     */
    private $timeout = 30;

    /**
     * The interval duration between each retry attempt in microseconds.
     *
     * @var int the retry interval in microseconds
     */
    private $retryInterval = 100000;

    /**
     * @param null|HttpProbe $probe
     * @psalm-param mixed $probe
     */
    public function __construct($probe = null)
    {
        if ($probe !== null) {
            self::ensureInstanceOf($probe, HttpProbe::class);

        }

        $this->probe = $probe ?: new HttpProbeGetHeaders();
    }

    /**
     * Sets the endpoint to be checked for readiness.
     *
     * @param string $endpoint the endpoint to be checked
     * @psalm-param mixed $endpoint
     *
     * @return $this
     */
    public function withEndpoint($endpoint)
    {
        self::ensureString($endpoint);

        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Sets the schema to be used in the endpoint URL.
     *
     * @param string $schema the schema to be used
     * @psalm-param mixed $schema
     *
     * @return $this
     */
    public function withSchema($schema)
    {
        self::ensureString($schema);

        $this->schema = $schema;

        return $this;
    }

    /**
     * Sets the host to be used in the endpoint URL.
     *
     * @param string $host the host to be used
     * @psalm-param mixed $host
     *
     * @return $this
     */
    public function withHost($host)
    {
        self::ensureString($host);

        $this->host = $host;

        return $this;
    }

    /**
     * Sets the path to be used in the endpoint URL.
     *
     * @param string $path the path to be used
     * @psalm-param mixed $path
     *
     * @return $this
     */
    public function withPath($path)
    {
        self::ensureString($path);

        $this->path = $path;

        return $this;
    }

    /**
     * Sets the port to be used in the endpoint URL.
     *
     * @param int $port the port to be used
     * @psalm-param mixed $port
     *
     * @return $this
     */
    public function withPort($port)
    {
        self::ensureIntOrDigitString($port);

        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535');
        }
        $port = (int) $port;

        $this->port = $port;

        return $this;
    }

    /**
     * Sets the expected HTTP response code for the endpoint.
     *
     * @param int $responseCode the expected HTTP response code
     * @psalm-param mixed $responseCode
     *
     * @return $this
     */
    public function withExpectedResponseCode($responseCode)
    {
        self::ensureIntRange($responseCode, 100, 599);

        $this->expectedResponseCode = $responseCode;

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
        $endpoint = $this->endpoint;
        if ($endpoint === null) {
            $schema = $this->schema !== null && $this->schema !== '' ? $this->schema : 'http';
            $host = $this->host !== null && $this->host !== '' ? $this->host : $instance->getHost();
            $port = $this->port;
            if ($port === null) {
                $ports = $instance->getExposedPorts();
                if (count($ports) === 0) {
                    throw new \LogicException('HTTP wait strategy requires an exposed port');
                }
                $port = $instance->getMappedPort($ports[0]);
                if ($port === null) {
                    throw new \LogicException('HTTP wait strategy requires a mapped port');
                }
            }
            $path = $this->path !== null && $this->path !== '' ? $this->path : '/';
            $endpoint = sprintf('%s://%s:%s%s', $schema, $host, $port, $path);
        }

        $this->logger()->debug(sprintf(
            'Waiting for http available: endpoint=%s, expect=%d, timeout=%d',
            $endpoint,
            $this->expectedResponseCode,
            $this->timeout
        ));

        while (1) {
            if (time() - $now > $this->timeout) {
                throw new WaitingTimeoutException($this->timeout);
            }
            if ($instance->isStopped()) {
                throw new ContainerStoppedException('Container stopped while waiting for HTTP endpoint');
            }
            if ($this->probe->available($endpoint, $this->expectedResponseCode)) {
                break;
            }
            usleep($this->retryInterval);
        }
    }
}
