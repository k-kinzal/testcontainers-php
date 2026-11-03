<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\NetworkAliasSetting;
use Testcontainers\Containers\Types\NetworkMode;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;
use UnexpectedValueException;

final class NetworkAliasSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testNetworkAliasesIsProvidedByNetworkAliasSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(NetworkAliasSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testNetworkAliasesFromStaticNetworkAlias()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);
        $network = NetworkMode::fromString(md5(uniqid()));
        $client->networkCreate($network);

        $container = (new NetworkAliasSettingWithStaticNetworkAliasContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withNetworkMode($network)
            ->withCommands(['sh', '-c', 'ping -c 1 my-service'])
        ;
        $instance = $container->start();

        self::assertStringStartsWith('PING my-service', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithNetworkAlias()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);
        $network = NetworkMode::fromString(md5(uniqid()));
        $client->networkCreate($network);

        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withAutoRemoveOnExit(false)
            ->withNetworkMode($network)
            ->withNetworkAlias('my-alias')
            ->withCommands(['sh', '-c', 'ping -c 1 my-alias'])
        ;
        $instance = $container->start();

        self::assertStringStartsWith('PING my-alias', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithNetworkAliases()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);
        $network = NetworkMode::fromString(md5(uniqid()));
        $client->networkCreate($network);

        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withAutoRemoveOnExit(false)
            ->withNetworkMode($network)
            ->withNetworkAliases(['my-alias'])
            ->withCommands(['sh', '-c', 'ping -c 1 my-alias'])
        ;
        $instance = $container->start();

        self::assertStringStartsWith('PING my-alias', $instance->getOutput());
    }

    /**
     * @dataProvider providerWithNetworkAliasesRejectsInvalidInputs
     * @param array{string, array{}} $aliases
     * @return void
     */
    public function testWithNetworkAliasesRejectsInvalidInputs($aliases)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withNetworkAliases($aliases);
    }

    /**
     * @return array<string, array{array{string, array{}}}>
     */
    public static function providerWithNetworkAliasesRejectsInvalidInputs()
    {
        return [
            'case 1' => [['ok', []]],
        ];
    }

    /**
     * @return void
     */
    public function testNetworkAliasesRejectsInvalidStaticConfiguration()
    {
        $container = new NetworkAliasSettingWithInvalidNetworkAliasesContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class NetworkAliasSettingWithStaticNetworkAliasContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = false;

    protected static $NETWORK_ALIASES = ['my-service'];
}

final class NetworkAliasSettingWithInvalidNetworkAliasesContainer extends GenericContainer
{
    protected static $NETWORK_ALIASES = [false];
}
