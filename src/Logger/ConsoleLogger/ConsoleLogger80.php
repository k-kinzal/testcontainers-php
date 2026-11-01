<?php

namespace Testcontainers\Logger\ConsoleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use Stringable;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Ensure;

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
 * @api
 */
class ConsoleLogger80 extends AbstractLogger
{
    use Ensure;

    /**
     * The severity of each supported PSR-3 log level.
     *
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
     * The output stream for formatted log messages.
     *
     * @var resource
     */
    private $stream;

    /**
     * The minimum log level to write to the output stream.
     *
     * @var string
     */
    private $minLevel;

    /**
     * Write messages at or above the given level to the supplied stream.
     *
     * @param resource|null $stream   Stream to write to. Defaults to STDERR.
     * @param string        $minLevel Minimum PSR-3 log level to output. Defaults to DEBUG (all messages).
     * @psalm-param mixed $stream
     * @psalm-param mixed $minLevel
     */
    public function __construct($stream = null, $minLevel = LogLevel::DEBUG)
    {
        self::ensureNullableStream($stream);
        self::ensureOneOf($minLevel, array_keys(self::$LOG_LEVELS));

        if ($stream === null) {
            $stream = defined('STDERR') ? STDERR : fopen('php://stderr', 'w');
            self::ensureStream($stream);
        }

        $this->stream = $stream;
        $this->minLevel = $minLevel;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param string            $level
     * @param string|Stringable $message
     * @param array             $context
     * @psalm-param mixed $level
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        try {
            self::ensureOneOf($level, array_keys(self::$LOG_LEVELS));
        } catch (InvalidFormatException $e) {
            throw new InvalidArgumentException(sprintf('Invalid log level: %s', var_export($level, true)), 0, $e);
        }

        if (self::$LOG_LEVELS[$level] < self::$LOG_LEVELS[$this->minLevel]) {
            return;
        }

        $message = $this->interpolate((string) $message, $context);
        $contextStr = !empty($context) ? ' ' . (string) json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
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
        foreach (array_keys($context) as $key) {
            if (is_string($context[$key]) || is_numeric($context[$key]) || (is_object($context[$key]) && method_exists($context[$key], '__toString'))) {
                $replace['{' . $key . '}'] = (string) $context[$key];
            }
        }

        return strtr($message, $replace);
    }
}
