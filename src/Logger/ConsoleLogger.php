<?php

namespace Testcontainers\Logger;

if (PHP_VERSION_ID < 80000) {
    class_alias(ConsoleLogger\ConsoleLogger56::class, ConsoleLoggerBase::class);
} else {
    class_alias(ConsoleLogger\ConsoleLogger80::class, ConsoleLoggerBase::class);
}

/**
 * Write log messages using the implementation supported by the current PHP version.
 *
 * @api
 */
class ConsoleLogger extends ConsoleLoggerBase
{
}
