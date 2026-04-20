<?php

namespace Testcontainers\Utility;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function Testcontainers\ensure;

/**
 * WithLogger trait provides a way to set and get a logger instance.
 */
trait WithLogger
{
    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * Set Logger instance.
     *
     * @param LoggerInterface $logger the logger instance
     *
     * @return self
     */
    public function withLogger($logger)
    {
        ensure($logger instanceof LoggerInterface, '$logger must be LoggerInterface');

        $this->logger = $logger;

        return $this;
    }

    /**
     * Get Logger instance.
     *
     * @return LoggerInterface
     */
    public function logger()
    {
        return $this->logger ?: new NullLogger();
    }
}
