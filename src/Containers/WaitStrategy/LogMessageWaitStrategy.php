<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Docker\DockerClientFactory;

/**
 * A wait strategy that pauses execution until a specific log message appears in the container logs.
 *
 * This strategy is useful for ensuring that a container is fully initialized and ready for use
 * by waiting for a predefined log message to be output by the container.
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
        foreach ($iter as $line) {
            if (preg_match('/'.preg_quote($this->pattern, '/').'/', trim($line))) {
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
