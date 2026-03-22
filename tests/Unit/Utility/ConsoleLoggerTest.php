<?php

namespace Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Testcontainers\Utility\ConsoleLogger;

/**
 * @internal
 *
 * @coversNothing
 *
 * @requires PHP 8.0
 */
class ConsoleLoggerTest extends TestCase
{
    public function testImplementsLoggerInterface()
    {
        $logger = new ConsoleLogger($this->createStream());

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function testDebugMessageWritesToStream()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->debug('hello world');

        $output = $this->readStream($stream);
        $this->assertMatchesRegularExpression('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \[DEBUG\] hello world$/', trim($output));
    }

    public function testMessageWithContext()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->info('User logged in', ['name' => 'Alice']);

        $output = $this->readStream($stream);
        $this->assertStringContainsString('[INFO] User logged in {"name":"Alice"}', $output);
    }

    public function testEmptyContextOmitsJson()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->warning('something happened');

        $output = trim($this->readStream($stream));
        $this->assertStringEndsWith('something happened', $output);
        $this->assertStringNotContainsString('{}', $output);
    }

    public function testPlaceholderInterpolation()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->debug('Connecting to {host}:{port}', ['host' => 'localhost', 'port' => 3306]);

        $output = $this->readStream($stream);
        $this->assertStringContainsString('Connecting to localhost:3306', $output);
    }

    public function testMinLevelFiltering()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream, LogLevel::WARNING);

        $logger->debug('hidden');
        $logger->info('also hidden');
        $logger->warning('visible');

        $output = $this->readStream($stream);
        $this->assertStringNotContainsString('hidden', $output);
        $this->assertStringContainsString('[WARNING] visible', $output);
    }

    public function testAllLevelsOutputWhenDebug()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->emergency('msg');
        $logger->alert('msg');
        $logger->critical('msg');
        $logger->error('msg');
        $logger->warning('msg');
        $logger->notice('msg');
        $logger->info('msg');
        $logger->debug('msg');

        $output = $this->readStream($stream);
        $this->assertStringContainsString('[EMERGENCY]', $output);
        $this->assertStringContainsString('[ALERT]', $output);
        $this->assertStringContainsString('[CRITICAL]', $output);
        $this->assertStringContainsString('[ERROR]', $output);
        $this->assertStringContainsString('[WARNING]', $output);
        $this->assertStringContainsString('[NOTICE]', $output);
        $this->assertStringContainsString('[INFO]', $output);
        $this->assertStringContainsString('[DEBUG]', $output);
    }

    public function testLevelDisplayedUppercase()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->error('fail');

        $output = $this->readStream($stream);
        $this->assertStringContainsString('[ERROR]', $output);
    }

    public function testContextWithNonStringableValues()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->error('Failed', [
            'exception' => new \RuntimeException('test'),
            'nested' => ['a' => 1],
            'null_val' => null,
        ]);

        $output = $this->readStream($stream);
        $this->assertStringContainsString('[ERROR] Failed', $output);
        $this->assertStringContainsString('"nested":{"a":1}', $output);
    }

    /**
     * @return resource
     */
    private function createStream()
    {
        return fopen('php://memory', 'r+');
    }

    /**
     * @param resource $stream
     *
     * @return string
     */
    private function readStream($stream)
    {
        rewind($stream);

        return stream_get_contents($stream);
    }
}
