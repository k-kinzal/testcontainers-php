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
        $this->assertTrue(preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \[DEBUG\] hello world$/', trim($output)) === 1);
    }

    public function testMessageWithContext()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->info('User logged in', array('name' => 'Alice'));

        $output = $this->readStream($stream);
        $this->assertTrue(strpos($output, '[INFO] User logged in {"name":"Alice"}') !== false);
    }

    public function testEmptyContextOmitsJson()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->warning('something happened');

        $output = trim($this->readStream($stream));
        $this->assertStringEndsWith('something happened', $output);
        $this->assertTrue(strpos($output, '{}') === false);
    }

    public function testPlaceholderInterpolation()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->debug('Connecting to {host}:{port}', array('host' => 'localhost', 'port' => 3306));

        $output = $this->readStream($stream);
        $this->assertTrue(strpos($output, 'Connecting to localhost:3306') !== false);
    }

    public function testMinLevelFiltering()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream, LogLevel::WARNING);

        $logger->debug('hidden');
        $logger->info('also hidden');
        $logger->warning('visible');

        $output = $this->readStream($stream);
        $this->assertTrue(strpos($output, 'hidden') === false);
        $this->assertTrue(strpos($output, '[WARNING] visible') !== false);
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
        $this->assertTrue(strpos($output, '[EMERGENCY]') !== false);
        $this->assertTrue(strpos($output, '[ALERT]') !== false);
        $this->assertTrue(strpos($output, '[CRITICAL]') !== false);
        $this->assertTrue(strpos($output, '[ERROR]') !== false);
        $this->assertTrue(strpos($output, '[WARNING]') !== false);
        $this->assertTrue(strpos($output, '[NOTICE]') !== false);
        $this->assertTrue(strpos($output, '[INFO]') !== false);
        $this->assertTrue(strpos($output, '[DEBUG]') !== false);
    }

    public function testLevelDisplayedUppercase()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->error('fail');

        $output = $this->readStream($stream);
        $this->assertTrue(strpos($output, '[ERROR]') !== false);
    }

    public function testContextWithNonStringableValues()
    {
        $stream = $this->createStream();
        $logger = new ConsoleLogger($stream);

        $logger->error('Failed', array(
            'exception' => new \RuntimeException('test'),
            'nested' => array('a' => 1),
            'null_val' => null,
        ));

        $output = $this->readStream($stream);
        $this->assertTrue(strpos($output, '[ERROR] Failed') !== false);
        $this->assertTrue(strpos($output, '"nested":{"a":1}') !== false);
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
