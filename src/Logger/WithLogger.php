<?php

namespace Testcontainers\Logger;

use Testcontainers\Utility\Ensure;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * WithLogger trait provides a way to set and get a logger instance.
 */
trait WithLogger
{
    // Import Ensure once through this shared trait; PHP 5.6 rejects duplicate imports.
    use Ensure;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * Set Logger instance.
     *
     * @param LoggerInterface $logger the logger instance
     * @psalm-param mixed $logger
     *
     * @return self
     */
    public function withLogger($logger)
    {
        self::ensureInstanceOf($logger, LoggerInterface::class);

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
