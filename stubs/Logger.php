<?php

namespace Psr\Log;

/**
 * PSR-3 1.1 signatures used by the PHP 5.6 implementation.
 */
interface LoggerInterface
{
    /**
     * Log a emergency message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function emergency($message, array $context = []);

    /**
     * Log a alert message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function alert($message, array $context = []);

    /**
     * Log a critical message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function critical($message, array $context = []);

    /**
     * Log a error message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function error($message, array $context = []);

    /**
     * Log a warning message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function warning($message, array $context = []);

    /**
     * Log a notice message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function notice($message, array $context = []);

    /**
     * Log a info message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function info($message, array $context = []);

    /**
     * Log a debug message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function debug($message, array $context = []);

    /**
     * Log a message at the supplied level.
     *
     * @param  mixed         $level   the PSR-3 log level
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function log($level, $message, array $context = []);
}

/**
 * PSR-3 1.1 forwarding methods have no native parameter or return types.
 */
abstract class AbstractLogger implements LoggerInterface
{
    /**
     * Log a emergency message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function emergency($message, array $context = [])
    {
    }

    /**
     * Log a alert message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function alert($message, array $context = [])
    {
    }

    /**
     * Log a critical message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function critical($message, array $context = [])
    {
    }

    /**
     * Log a error message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function error($message, array $context = [])
    {
    }

    /**
     * Log a warning message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function warning($message, array $context = [])
    {
    }

    /**
     * Log a notice message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function notice($message, array $context = [])
    {
    }

    /**
     * Log a info message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function info($message, array $context = [])
    {
    }

    /**
     * Log a debug message.
     *
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function debug($message, array $context = [])
    {
    }
}

/**
 * Logger that discards messages under PSR-3 1.1.
 */
class NullLogger extends AbstractLogger
{
    /**
     * Log a message at the supplied level.
     *
     * @param  mixed         $level   the PSR-3 log level
     * @param  string|object $message the message to log
     * @param  array         $context additional log context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
    }
}

/**
 * Standard PSR-3 log level names.
 */
class LogLevel
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
}
