<?php

namespace Testcontainers\Utility;

if (PHP_VERSION_ID >= 80000) {
    require_once __DIR__ . '/ConsoleLogger/ConsoleLogger80.php';
} else {
    require_once __DIR__ . '/ConsoleLogger/ConsoleLogger56.php';
}
