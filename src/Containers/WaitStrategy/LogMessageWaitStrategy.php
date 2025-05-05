<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Utility\WithLogger;

/**
 * LogMessageWaitStrategy waits until a specified log message is found in the container logs.
 *
 * This strategy continuously checks the container logs for a specified log message
 * until it is found or a timeout occurs.
 */
class LogMessageWaitStrategy implements WaitStrategy
{
    use WithLogger;

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
     * Sets the pattern to be used for matching log messages.
     *
     * @param string $pattern the regex pattern to match against log messages
     *
     * @return $this the current instance for method chaining
     */
    public function withPattern($pattern)
    {
        $this->pattern = $pattern;

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
        $containerId = $instance->getContainerId();

        $client = DockerClientFactory::create();
        $output = $client->withTimeout($this->timeout)->logs($containerId, ['follow' => true]);
        if (!$output instanceof DockerFollowLogsOutput) {
            throw new \LogicException('Expected DockerFollowLogsOutput instance: `'.get_class($output).'`');
        }
        $iter = $output->getIterator();
        $pattern = '/'.str_replace('/', '\/', $this->pattern).'/';
        $this->logger()->debug('Waiting for log message: pattern='.$pattern);
        foreach ($iter as $line) {
            $this->logger()->debug(trim($line));
            if (preg_match($pattern, trim($line))) {
                return;
            }
        }
    }
}
