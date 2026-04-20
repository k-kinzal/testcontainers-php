<?php

namespace Testcontainers\Containers\WaitStrategy;

use LogicException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Utility\WithLogger;

use function Testcontainers\ensure;

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
     * The regex pattern used to detect failure log messages.
     *
     * When set, if a log line matches this pattern, the strategy immediately
     * throws a LogMessageFailedException instead of waiting until timeout.
     *
     * @var null|string
     */
    private $failurePattern;

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
        ensure(is_string($pattern), '$pattern must be string');

        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Sets the failure pattern to detect error log messages.
     *
     * When a log line matches this pattern, the strategy immediately throws
     * a LogMessageFailedException instead of waiting until timeout.
     *
     * @param string $pattern the regex pattern to match against log messages
     *
     * @return $this the current instance for method chaining
     */
    public function withFailurePattern($pattern)
    {
        ensure(is_string($pattern), '$pattern must be string');

        $this->failurePattern = $pattern;

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
        ensure(is_int($seconds), '$seconds must be int');

        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Waits until the container instance is ready.
     *
     * @param ContainerInstance $instance the container instance to check
     *
     * @return void
     *
     * @throws LogMessageFailedException if the failure pattern matches a log line
     * @throws WaitingTimeoutException   if the timeout duration is exceeded
     * @throws ContainerStoppedException if the container stops while waiting
     * @throws NoSuchContainerException  if the container no longer exists
     * @throws DockerException           if the Docker command fails
     */
    public function waitUntilReady($instance)
    {
        ensure($instance instanceof ContainerInstance, '$instance must be ContainerInstance');

        $containerId = $instance->getContainerId();

        $client = DockerClientFactory::create();
        $output = $client->withTimeout($this->timeout)->logs($containerId, ['follow' => true]);
        if (!$output instanceof DockerFollowLogsOutput) {
            throw new LogicException('Expected DockerFollowLogsOutput instance: `'.get_class($output).'`');
        }
        $iter = $output->getIterator();
        $pattern = '/'.str_replace('/', '\/', $this->pattern).'/';
        $this->logger()->debug('Waiting for log message: pattern='.$pattern);

        $failurePattern = null;
        if ($this->failurePattern !== null) {
            $failurePattern = '/'.str_replace('/', '\/', $this->failurePattern).'/';
            $this->logger()->debug('Failure pattern='.$failurePattern);
        }

        try {
            foreach ($iter as $line) {
                $this->logger()->debug(trim($line));
                if ($failurePattern !== null && preg_match($failurePattern, trim($line))) {
                    throw new LogMessageFailedException(trim($line));
                }
                if (preg_match($pattern, trim($line))) {
                    return;
                }
            }
        } catch (ProcessTimedOutException $e) {
            throw new WaitingTimeoutException($this->timeout);
        }

        throw new ContainerStoppedException('Container stopped while waiting for log message');
    }
}
