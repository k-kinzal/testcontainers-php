<?php

namespace Testcontainers\Logger;

// The alias selects the implementation without redefining the public class.
if (PHP_VERSION_ID < 80000) {
    class_alias(ConsoleLogger\ConsoleLogger56::class, ConsoleLoggerBase::class);
} else {
    class_alias(ConsoleLogger\ConsoleLogger80::class, ConsoleLoggerBase::class);
}

/** @api */
class ConsoleLogger extends ConsoleLoggerBase
{
}
