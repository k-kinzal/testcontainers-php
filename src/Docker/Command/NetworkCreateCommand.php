<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Output\DockerNetworkCreateOutput;
use Testcontainers\Utility\Stringable;

/**
 * Network create command for Docker.
 *
 * This trait provides methods to create a network using the `docker network create` command.
 */
trait NetworkCreateCommand
{
    /**
     * Create a new Docker network.
     *
     * This method wraps the `docker network create` command to create a new Docker network.
     *
     * @param string $network the name of the Docker network to create
     * @param array{
     *     attachable?: null|bool,
     *     auxAddress?: null|string[]|Stringable[],
     *     configFrom?: null|string|Stringable,
     *     configOnly?: null|bool,
     *     driver?: null|string|Stringable,
     *     gateway?: null|string|Stringable,
     *     ingress?: null|bool,
     *     internal?: null|bool,
     *     ipRange?: null|string[]|Stringable[],
     *     ipamDriver?: null|string|Stringable,
     *     ipamOpt?: null|array<string, string|Stringable>,
     *     ipv6?: null|bool,
     *     label?: null|string[]|Stringable[],
     *     opt?: null|array<string, string|Stringable>,
     *     scope?: null|string|Stringable,
     *     subnet?: null|string[]|Stringable[],
     * } $options Additional options for the Docker network create command
     *
     * @return DockerNetworkCreateOutput the output of the Docker network create command
     */
    public function networkCreate($network, $options = [])
    {
        $process = $this->execute(
            'network',
            'create',
            [$network],
            $options
        );

        return new DockerNetworkCreateOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
