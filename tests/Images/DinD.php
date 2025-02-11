<?php

namespace Tests\Images;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;

/**
 * Docker in Docker container
 */
class DinD extends GenericContainer
{
    /**
     * The Docker image to be used for the DinD container.
     *
     * @var string
     */
    protected static $IMAGE = 'docker:27.4.1-dind';

    /**
     * The commands to be executed in the DinD container.
     *
     * Note: The `--tls=false` option is included to prevent slow performance.
     *
     * @var string[]
     */
    protected static $COMMANDS = [
        'dockerd-entrypoint.sh',
        '--tls=false'
    ];

    /**
     * The environment variables to be set in the DinD container.
     *
     * Note: TLS is disabled for performance and ease of use.
     *
     * @var array
     */
    protected static $ENVIRONMENTS = [
        'DOCKER_TLS_CERTDIR' => ''
    ];

    /**
     * The ports to be exposed by the DinD container.
     *
     * @var int[]
     */
    protected static $PORTS = [2375];

    /**
     * Whether the DinD container should run in privileged mode.
     *
     * @var bool
     */
    protected static $PRIVILEGED = true;

    /**
     * {@inheritdoc}
     */
    protected function waitStrategy($instance)
    {
        return (new HttpWaitStrategy())
            ->withSchema('http')
            ->withPath('/_ping');
    }
}
