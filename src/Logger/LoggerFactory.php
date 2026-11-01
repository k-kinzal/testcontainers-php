<?php

namespace Testcontainers\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;
use Testcontainers\Environments;

/**
 * A factory for creating logger instances from environment variables.
 *
 * This class provides a static method for creating a logger based on the
 * `TESTCONTAINERS_LOGGER` and `TESTCONTAINERS_LOG_LEVEL` environment variables.
 * @api
 */
class LoggerFactory
{
    /**
     * Create and return a new logger instance.
     *
     * This method creates an instance of the class specified by `TESTCONTAINERS_LOGGER`.
     * If it is not specified, or `ConsoleLogger` is specified, a `ConsoleLogger` is created
     * with the level specified by `TESTCONTAINERS_LOG_LEVEL`. The level defaults to `debug`
     * when the debug mode of the CI service is enabled. If neither the class nor the level
     * is specified, a `NullLogger` is returned.
     *
     * @return LoggerInterface the logger instance
     *
     * @throws \InvalidArgumentException if `TESTCONTAINERS_LOGGER` is not a class implementing `LoggerInterface`
     */
    public static function create()
    {
        $class = Environments::TESTCONTAINERS_LOGGER();
        $level = Environments::TESTCONTAINERS_LOG_LEVEL();
        if ($level === null) {
            foreach (['RUNNER_DEBUG', 'ACTIONS_STEP_DEBUG', 'CI_DEBUG_TRACE', 'SYSTEM_DEBUG'] as $name) {
                if (in_array(strtolower((string) Environments::$name()), ['1', 'true'], true)) {
                    $level = LogLevel::DEBUG;

                    break;
                }
            }
        }

        if ($class === null && $level === null) {
            return new NullLogger();
        }
        if ($class === null || ltrim($class, '\\') === ConsoleLogger::class) {
            return new ConsoleLogger(null, $level ?: LogLevel::DEBUG);
        }
        if (!is_subclass_of($class, LoggerInterface::class)) {
            throw new \InvalidArgumentException(sprintf('TESTCONTAINERS_LOGGER must be a class implementing %s: "%s"', LoggerInterface::class, $class));
        }

        return new $class();
    }
}
