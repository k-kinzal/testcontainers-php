<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Docker\DockerClient;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class BaseCommandTest extends TestCase
{
    /**
     * @dataProvider providerWithGlobalOptionsInvalidHosts
     * @param bool|int|array{}|array{bool}|stdClass $host
     * @return void
     */
    public function testWithGlobalOptionsRejectsInvalidHost($host)
    {
        $client = new DockerClient();
        $this->expectException(UnexpectedValueException::class);

        $client->withGlobalOptions(['host' => $host]);
    }

    /**
     * @return array<string, array{bool|int|array{}|array{bool}|stdClass}>
     */
    public static function providerWithGlobalOptionsInvalidHosts()
    {
        return [
            'boolean' => [false],
            'integer' => [1],
            'empty array' => [[]],
            'boolean array' => [[false]],
            'unrelated object' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerWithGlobalOptionsHosts
     * @param string|non-empty-array<array-key, string> $host
     * @param string $expected
     * @return void
     */
    public function testWithGlobalOptionsPreservesFirstHost($host, $expected)
    {
        $client = (new DockerClient())->withGlobalOptions(['host' => $host]);

        $this->assertSame($expected, $client->getHost());
    }

    /**
     * @return array<string, array{string|non-empty-array<array-key, string>, string}>
     */
    public static function providerWithGlobalOptionsHosts()
    {
        return [
            'string' => ['tcp://first:2375', 'tcp://first:2375'],
            'list' => [['tcp://first:2375', 'tcp://second:2375'], 'tcp://first:2375'],
            'map' => [['first' => 'tcp://first:2375', 'second' => 'tcp://second:2375'], 'tcp://first:2375'],
        ];
    }

    /**
     * @return void
     */
    public function testWithGlobalOptionsRejectsUnrenderableValue()
    {
        $client = new DockerClient();
        $this->expectException(UnexpectedValueException::class);

        $client->withGlobalOptions(['value' => new stdClass()]);
    }

    /**
     * @return void
     */
    public function testWithProcOptionsExecutesCommand()
    {
        $client = (new DockerClient())
            ->withCommand(PHP_BINARY)
            ->withGlobalOptions(['version' => true])
            ->withProcOptions(['blocking_pipes' => false])
        ;
        $output = $client->version();

        $this->assertSame(0, $output->getExitCode());
        $this->assertSame(0, strpos($output->getOutput(), 'PHP '));
    }

    /**
     * @return void
     */
    public function testGetHostFromDefault()
    {
        $backup = getenv('DOCKER_HOST');

        try {
            putenv('DOCKER_HOST');
            $client = new DockerClient();

            $this->assertSame('unix:///var/run/docker.sock', $client->getHost());
        } finally {
            putenv($backup === false ? 'DOCKER_HOST' : 'DOCKER_HOST='.$backup);
        }
    }

    /**
     * @return void
     */
    public function testGetHostFromOptions()
    {
        $client = (new DockerClient())
            ->withGlobalOptions([
                'host' => 'tcp://127.0.0.1:2375',
            ])
        ;

        $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
    }

    /**
     * @return void
     */
    public function testGetHostFromEnv()
    {
        $client = (new DockerClient())
            ->withEnv([
                'DOCKER_HOST' => 'tcp://127.0.0.1:2375',
            ])
        ;

        $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
    }

    /**
     * @return void
     */
    public function testGetHostFromGlobalEnv()
    {
        $backup = getenv('DOCKER_HOST');

        try {
            putenv('DOCKER_HOST=tcp://127.0.0.1:2375');
            $client = new DockerClient();

            $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
        } finally {
            putenv($backup === false ? 'DOCKER_HOST' : 'DOCKER_HOST='.$backup);
        }
    }
}
