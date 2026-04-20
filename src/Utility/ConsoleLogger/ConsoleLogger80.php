<?php

namespace Testcontainers\Utility;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

use function Testcontainers\ensure;

/**
 * A simple PSR-3 logger that writes formatted messages to a stream.
 *
 * Designed for environments where a full logging library (Monolog, Symfony Console)
 * is not available. Zero configuration needed - defaults to writing to STDERR.
 *
 * Usage:
 *   $container->withLogger(new ConsoleLogger());
 *   $container->withLogger(new ConsoleLogger(null, LogLevel::WARNING));
 *   $container->withLogger(new ConsoleLogger(fopen('/tmp/tc.log', 'a')));
 */
class ConsoleLogger extends AbstractLogger
{
    /**
     * @var array<string, int>
     */
    private static $LOG_LEVELS = [
        LogLevel::EMERGENCY => 7,
        LogLevel::ALERT => 6,
        LogLevel::CRITICAL => 5,
        LogLevel::ERROR => 4,
        LogLevel::WARNING => 3,
        LogLevel::NOTICE => 2,
        LogLevel::INFO => 1,
        LogLevel::DEBUG => 0,
    ];

    /**
     * @var resource
     */
    private $stream;

    /**
     * @var string
     */
    private $minLevel;

    /**
     * @param resource|null $stream   Stream to write to. Defaults to STDERR.
     * @param string        $minLevel Minimum PSR-3 log level to output. Defaults to DEBUG (all messages).
     */
    public function __construct($stream = null, $minLevel = LogLevel::DEBUG)
    {
        ensure($stream === null || is_resource($stream), '$stream must be null|resource');
        ensure(is_string($minLevel), '$minLevel must be string');

        if ($stream !== null) {
            $this->stream = $stream;
        } elseif (defined('STDERR')) {
            $this->stream = STDERR;
        } else {
            $this->stream = fopen('php://stderr', 'w');
        }

        if (!isset(self::$LOG_LEVELS[$minLevel])) {
            throw new InvalidArgumentException(sprintf('Invalid minimum log level: "%s"', $minLevel));
        }

        $this->minLevel = $minLevel;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        ensure(is_string($level), '$level must be string');

        if (!isset(self::$LOG_LEVELS[$level])) {
            throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
        }

        if (self::$LOG_LEVELS[$level] < self::$LOG_LEVELS[$this->minLevel]) {
            return;
        }

        $message = $this->interpolate($message, $context);
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $line = sprintf("[%s] [%s] %s%s\n", date('Y-m-d H:i:s'), strtoupper($level), $message, $contextStr);

        fwrite($this->stream, $line);
    }

    /**
     * Interpolate context values into message placeholders.
     *
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    private function interpolate($message, array $context)
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || is_numeric($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = (string) $val;
            }
        }

        return strtr($message, $replace);
    }
}
