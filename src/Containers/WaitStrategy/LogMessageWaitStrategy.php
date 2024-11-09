<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Docker\DockerClientFactory;

/**
 * LogMessageWaitStrategy waits until a specified log message is found in the container logs.
 *
 * This strategy continuously checks the container logs for a specified log message
 * until it is found or a timeout occurs.
 */
class LogMessageWaitStrategy implements WaitStrategy
{
    /**
     * The regex pattern used to match log messages.
     *
     * @var string
     */
    private $pattern = '.*';


    /**
     * The timeout duration in seconds for waiting until the container instance is ready.
     *
     * @var int
     */
    private $timeout = 30;

    /**
     * {@inheritdoc}
     */
    public function waitUntilReady($instance)
    {
        $containerId = $instance->getContainerId();

        $client = DockerClientFactory::create();
        $output = $client->withTimeout($this->timeout)->followLogs($containerId);
        $iter = $output->getIterator();
        $pattern = '/'.str_replace('/', '\/', $this->pattern).'/';
        foreach ($iter as $line) {
            if (preg_match($pattern, trim($line))) {
                return;
            }
        }
    }

    /**
     * Sets the pattern to be used for matching log messages.
     *
     * @param string $pattern The regex pattern to match against log messages.
     * @return $this The current instance for method chaining.
     */
    public function withPattern($pattern)
    {
        $this->pattern = $pattern;

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
     * @inheritDoc
     */
    public function getName()
    {
        return 'log_message';
    }
}