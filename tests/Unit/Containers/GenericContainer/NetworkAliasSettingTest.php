<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\NetworkAliasSetting;
use Testcontainers\Containers\Types\NetworkMode;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

class NetworkAliasSettingTest extends TestCase
{
    public function testHasNetworkAliasSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(NetworkAliasSetting::class, $uses);
    }

    public function testStaticNetworkAlias()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
                'config' => __ROOT__ . '/config.json',
            ],
        ]);
        $network = NetworkMode::fromString(md5(uniqid()));
        $client->networkCreate($network);

        $container = (new NetworkAliasSettingWithStaticNetworkAliasContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withNetworkMode($network)
            ->withCommands(['sh', '-c', 'ping -c 1 my-service'])
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING my-service', $instance->getOutput());
    }

    public function testStartWithNetworkAlias()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
                'config' => __ROOT__ . '/config.json',
            ],
        ]);
        $network = NetworkMode::fromString(md5(uniqid()));
        $client->networkCreate($network);

        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withNetworkMode($network)
            ->withNetworkAlias('my-alias')
            ->withCommands(['sh', '-c', 'ping -c 1 my-alias'])
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING my-alias', $instance->getOutput());
    }

    public function testStartWithNetworkAliases()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
                'config' => __ROOT__ . '/config.json',
            ],
        ]);
        $network = NetworkMode::fromString(md5(uniqid()));
        $client->networkCreate($network);

        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withNetworkMode($network)
            ->withNetworkAliases(['my-alias'])
            ->withCommands(['sh', '-c', 'ping -c 1 my-alias'])
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING my-alias', $instance->getOutput());
    }
}

class NetworkAliasSettingWithStaticNetworkAliasContainer extends GenericContainer
{
    protected static $NETWORK_ALIASES = ['my-service'];
}
