<?php

namespace Tests\Unit\Docker\Output;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Output\DockerVersionOutput;

final class DockerVersionOutputTest extends TestCase
{
    /**
     * @dataProvider providerGetClientVersionReturnsParsedVersion
     * @param string      $json
     * @param null|string $expected
     * @return void
     */
    public function testGetClientVersionReturnsParsedVersion($json, $expected)
    {
        $process = new Process([PHP_BINARY, '-r', 'echo $argv[1];', $json]);
        $process->run();
        $output = new DockerVersionOutput($process);

        self::assertSame($expected, $output->getClientVersion());
    }

    /**
     * @return array<string, array{string, null|string}>
     */
    public static function providerGetClientVersionReturnsParsedVersion()
    {
        return [
            'both versions' => ['{"Client":{"Version":"28.0.1"},"Server":{"Version":"28.0.2"}}', '28.0.1'],
            'client only' => ['{"Client":{"Version":"28.0.1"}}', '28.0.1'],
            'server only' => ['{"Server":{"Version":"28.0.2"}}', null],
            'missing version' => ['{"Client":{}}', null],
            'null version' => ['{"Client":{"Version":null}}', null],
            'null client' => ['{"Client":null}', null],
            'empty version' => ['{"Client":{"Version":""}}', ''],
            'empty object' => ['{}', null],
            'null output' => ['null', null],
            'scalar output' => ['false', null],
            'invalid JSON' => ['invalid', null],
            'empty output' => ['', null],
        ];
    }

    /**
     * @dataProvider providerGetServerVersionReturnsParsedVersion
     * @param string      $json
     * @param null|string $expected
     * @return void
     */
    public function testGetServerVersionReturnsParsedVersion($json, $expected)
    {
        $process = new Process([PHP_BINARY, '-r', 'echo $argv[1];', $json]);
        $process->run();
        $output = new DockerVersionOutput($process);

        self::assertSame($expected, $output->getServerVersion());
    }

    /**
     * @return array<string, array{string, null|string}>
     */
    public static function providerGetServerVersionReturnsParsedVersion()
    {
        return [
            'both versions' => ['{"Client":{"Version":"28.0.1"},"Server":{"Version":"28.0.2"}}', '28.0.2'],
            'server only' => ['{"Server":{"Version":"28.0.2"}}', '28.0.2'],
            'client only' => ['{"Client":{"Version":"28.0.1"}}', null],
            'missing version' => ['{"Server":{}}', null],
            'null version' => ['{"Server":{"Version":null}}', null],
            'null server' => ['{"Server":null}', null],
            'empty version' => ['{"Server":{"Version":""}}', ''],
            'empty object' => ['{}', null],
            'null output' => ['null', null],
            'scalar output' => ['false', null],
            'invalid JSON' => ['invalid', null],
            'empty output' => ['', null],
        ];
    }

    /**
     * @dataProvider providerConstructRejectsInvalidVersionType
     * @param string $json
     * @return void
     */
    public function testConstructRejectsInvalidVersionType($json)
    {
        $process = new Process([PHP_BINARY, '-r', 'echo $argv[1];', $json]);
        $process->run();
        $this->expectException(InvalidValueException::class);

        new DockerVersionOutput($process);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerConstructRejectsInvalidVersionType()
    {
        return [
            'integer client version' => ['{"Client":{"Version":28}}'],
            'boolean client version' => ['{"Client":{"Version":false}}'],
            'array client version' => ['{"Client":{"Version":[]}}'],
            'object client version' => ['{"Client":{"Version":{}}}'],
            'integer server version' => ['{"Server":{"Version":28}}'],
            'boolean server version' => ['{"Server":{"Version":false}}'],
            'array server version' => ['{"Server":{"Version":[]}}'],
            'object server version' => ['{"Server":{"Version":{}}}'],
        ];
    }
}
