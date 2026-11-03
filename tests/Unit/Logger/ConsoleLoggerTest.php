<?php

namespace Tests\Unit\Logger;

use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Logger\ConsoleLogger;

final class ConsoleLoggerTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructImplementsLoggerInterface()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);

        $logger = new ConsoleLogger($stream);

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    /**
     * @return void
     */
    public function testDebugMessageWritesToStream()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);

        $logger->debug('hello world');

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = $contents;
        $this->assertTrue(preg_match('/^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \[DEBUG\] hello world$/', trim($output)) === 1);
    }

    /**
     * @return void
     */
    public function testInfoWithContext()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);

        $logger->info('User logged in', array('name' => 'Alice'));

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = $contents;
        $this->assertTrue(strpos($output, '[INFO] User logged in {"name":"Alice"}') !== false);
    }

    /**
     * @return void
     */
    public function testWarningWithEmptyContextOmitsJson()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);

        $logger->warning('something happened');

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = trim($contents);
        $this->assertStringEndsWith('something happened', $output);
        $this->assertTrue(strpos($output, '{}') === false);
    }

    /**
     * @return void
     */
    public function testDebugInterpolatesPlaceholders()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);

        $logger->debug('Connecting to {host}:{port}', array('host' => 'localhost', 'port' => 3306));

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = $contents;
        $this->assertTrue(strpos($output, 'Connecting to localhost:3306') !== false);
    }

    /**
     * @return void
     */
    public function testLogFiltersBelowMinimumLevel()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream, LogLevel::WARNING);

        $logger->debug('hidden');
        $logger->info('also hidden');
        $logger->warning('visible');

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = $contents;
        $this->assertTrue(strpos($output, 'hidden') === false);
        $this->assertTrue(strpos($output, '[WARNING] visible') !== false);
    }

    /**
     * @return void
     */
    public function testLogOutputsAllLevelsWhenDebug()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);

        $logger->emergency('msg');
        $logger->alert('msg');
        $logger->critical('msg');
        $logger->error('msg');
        $logger->warning('msg');
        $logger->notice('msg');
        $logger->info('msg');
        $logger->debug('msg');

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = $contents;
        $this->assertTrue(strpos($output, '[EMERGENCY]') !== false);
        $this->assertTrue(strpos($output, '[ALERT]') !== false);
        $this->assertTrue(strpos($output, '[CRITICAL]') !== false);
        $this->assertTrue(strpos($output, '[ERROR]') !== false);
        $this->assertTrue(strpos($output, '[WARNING]') !== false);
        $this->assertTrue(strpos($output, '[NOTICE]') !== false);
        $this->assertTrue(strpos($output, '[INFO]') !== false);
        $this->assertTrue(strpos($output, '[DEBUG]') !== false);
    }

    /**
     * @return void
     */
    public function testErrorDisplaysLevelUppercase()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);

        $logger->error('fail');

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = $contents;
        $this->assertTrue(strpos($output, '[ERROR]') !== false);
    }

    /**
     * @return void
     */
    public function testErrorWithNonStringableContextValues()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);

        $logger->error('Failed', array(
            'exception' => new \RuntimeException('test'),
            'nested' => array('a' => 1),
            'null_val' => null,
        ));

        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $output = $contents;
        $this->assertTrue(strpos($output, '[ERROR] Failed') !== false);
        $this->assertTrue(strpos($output, '"nested":{"a":1}') !== false);
    }

    /**
     * @return void
     */
    public function testConstructRejectsInvalidMinimumLevel()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);

        $this->expectException(InvalidFormatException::class);

        new ConsoleLogger($stream, 'invalid');
    }

    /**
     * @return void
     */
    public function testLogRejectsInvalidLevel()
    {
        $stream = fopen('php://memory', 'r+');
        self::assertNotFalse($stream);

        $this->expectException(InvalidArgumentException::class);

        $logger = new ConsoleLogger($stream);
        $logger->log('invalid', 'message');
    }

}
