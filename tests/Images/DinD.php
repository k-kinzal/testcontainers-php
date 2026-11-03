<?php

namespace Tests\Images;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\Types\Mount;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Hook\AfterStartHook;

final class DinD extends GenericContainer
{
    use AfterStartHook;

    protected static $REUSE_MODE = 'reuse';

    protected static $AUTO_REMOVE_ON_EXIT = true;

    protected static $STOP_TIMEOUT = 0;

    protected static $IMAGE = 'docker:27.4.1-dind';

    protected static $COMMANDS = [
        'dockerd-entrypoint.sh',
        '--tls=false',
    ];

    protected static $ENVIRONMENTS = [
        'DOCKER_TLS_CERTDIR' => '',
    ];

    protected static $PORTS = [2375];

    protected static $PRIVILEGED = true;

    protected function waitStrategy($_instance)
    {
        return (new HttpWaitStrategy())
            ->withSchema('http')
            ->withPath('/_ping')
        ;
    }

    protected function mounts()
    {
        $tempDir = (string) realpath(sys_get_temp_dir());

        return array_merge(parent::mounts(), [Mount::fromString($tempDir.':'.$tempDir.':rw')]);
    }

    /**
     * @param ContainerInstance $instance
     * @return void
     * @psalm-suppress PossiblyUnusedMethod Called by Testcontainers::run().
     */
    public function afterStart($instance)
    {
        $port = $instance->getMappedPort(2375);
        $client = (new DockerClient())->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.(string) $port,
            'config' => __ROOT__.'/config.json',
        ]);

        $instance->setData($client);
    }
}
