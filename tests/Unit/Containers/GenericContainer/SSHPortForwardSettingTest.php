<?php

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Docker\DockerClient;
use UnexpectedValueException;

final class SSHPortForwardSettingTest extends TestCase
{
    /**
     * @dataProvider providerWithSSHPortForwardRejectsInvalidInputs
     * @param string $sshHost
     * @param string $sshPort
     * @return void
     */
    public function testWithSSHPortForwardRejectsInvalidInputs($sshHost, $sshPort)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withSSHPortForward($sshHost, $sshPort);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function providerWithSSHPortForwardRejectsInvalidInputs()
    {
        return [
            'case 1' => ['host', 'bad'],
        ];
    }

    /**
     * @return void
     */
    public function testSshPortForwardRejectsInvalidStaticConfigurationBeforeRunningContainer()
    {
        $client = (new DockerClient())->withCommand(__DIR__.'/missing-docker');
        $container = (new SSHPortForwardSettingWithInvalidSshPortForwardContainer('alpine'))
            ->withDockerClient($client)
            ->withExposedPort(80)
        ;
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class SSHPortForwardSettingWithInvalidSshPortForwardContainer extends GenericContainer
{
    protected static $SSH_PORT_FORWARD = [];
}
