<?php

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Docker\DockerClient;
use UnexpectedValueException;

/**
 * @internal
 * @coversNothing
 */
class SSHPortForwardSettingTest extends TestCase
{
    /**
     * @dataProvider provideWithSSHPortForwardInvalidInputs
     * @param mixed $sshHost
     * @param mixed $sshPort
     */
    public function testWithSSHPortForwardRejectsInvalidInputs($sshHost, $sshPort)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withSSHPortForward($sshHost, $sshPort);
    }

    /**
     * @return array
     */
    public static function provideWithSSHPortForwardInvalidInputs()
    {
        return [
            'case 1' => ['host', 'bad'],
        ];
    }

    public function testStartRejectsInvalidSshPortForwardBeforeRunningContainer()
    {
        $client = $this->createMock(DockerClient::class);
        $client->expects($this->never())->method('run');
        $container = (new SSHPortForwardSettingWithInvalidSshPortForwardContainer('alpine'))
            ->withDockerClient($client)
            ->withExposedPort(80)
        ;
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

class SSHPortForwardSettingWithInvalidSshPortForwardContainer extends GenericContainer
{
    protected static $SSH_PORT_FORWARD = [];
}
