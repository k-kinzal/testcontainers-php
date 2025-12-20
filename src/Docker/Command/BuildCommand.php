<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Output\DockerBuildOutput;
use Testcontainers\Utility\Stringable;

/**
 * Build command for Docker command.
 *
 * This trait provides a method for building a Docker image using the `docker build` command.
 */
trait BuildCommand
{
    /**
     * Build a Docker image from a Dockerfile.
     *
     * This method wraps the `docker build` command to build a Docker image from a specified Dockerfile.
     *
     * @param string $path the path to the directory containing the Dockerfile
     * @param array{
     *     addHost?: null|string[]|Stringable[],
     *     allow?: null|string[]|Stringable[],
     *     annotation?: null|string[]|Stringable[],
     *     attest?: null|string[]|Stringable[],
     *     buildArg?: null|string[]|Stringable[],
     *     buildContext?: null|string|Stringable,
     *     builder?: null|string|Stringable,
     *     cacheFrom?: null|string[]|Stringable[],
     *     cacheTo?: null|string[]|Stringable[],
     *     call?: null|string|Stringable,
     *     cgroupParent?: null|string|Stringable[],
     *     check?: null|bool,
     *     debug?: null|bool,
     *     file?: null|string|Stringable,
     *     iidfile?: null|string|Stringable,
     *     label?: null|string[]|Stringable[],
     *     load?: null|bool,
     *     metadataFile?: null|string|Stringable,
     *     network?: null|string|Stringable,
     *     noCache?: null|bool,
     *     noCacheFilter?: null|string|Stringable,
     *     output?: null|string|Stringable,
     *     platform?: null|string|Stringable,
     *     progress?: null|string|Stringable,
     *     provenance?: null|string|Stringable,
     *     pull?: null|bool,
     *     push?: null|bool,
     *     quiet?: null|bool,
     *     sbom?: null|string|Stringable,
     *     secret?: null|string[]|Stringable[],
     *     shmSize?: null|string|Stringable,
     *     ssh?: null|string|Stringable,
     *     tag?: null|string|Stringable,
     *     target?: null|string|Stringable,
     *     ulimit?: null|string|Stringable,
     * } $options Additional options for the Docker command
     *
     * @throws DockerException if the Docker command fails for any other reason
     *
     * @return DockerBuildOutput the output of the Docker build command
     */
    public function build($path, $options = [])
    {
        $process = $this->execute('build', null, [$path], $options);

        return new DockerBuildOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
