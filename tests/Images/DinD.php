<?php

namespace Tests\Images;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;

class DinD extends GenericContainer
{
    /**
     * @var string
     */
    protected static $IMAGE = 'docker:27.4.1-dind';

    /**
     * @var string[]
     */
    protected static $COMMANDS = [
        'dockerd-entrypoint.sh',
        '--tls=false',
    ];

    /**
     * @var array<string, string>
     */
    protected static $ENVIRONMENTS = [
        'DOCKER_TLS_CERTDIR' => '',
    ];

    /**
     * @var int[]
     */
    protected static $PORTS = [2375];

    /**
     * @var bool
     */
    protected static $PRIVILEGED = true;

    protected function waitStrategy($instance)
    {
        return (new HttpWaitStrategy())
            ->withSchema('http')
            ->withPath('/_ping')
        ;
    }
}
