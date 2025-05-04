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
     *     addHost?: string[]|Stringable[]|null,
     *     allow?: string[]|Stringable[]|null,
     *     annotation?: string[]|Stringable[]|null,
     *     attest?: string[]|Stringable[]|null,
     *     buildArg?: string[]|Stringable[]|null,
     *     buildContext?: string|Stringable|null,
     *     builder?: string|Stringable|null,
     *     cacheFrom?: string[]|Stringable[]|null,
     *     cacheTo?: string[]|Stringable[]|null,
     *     call?: string|Stringable|null,
     *     cgroupParent?: string|Stringable[]|null,
     *     check?: boolean|null,
     *     debug?: boolean|null,
     *     file?: string|Stringable|null,
     *     iidfile?: string|Stringable|null,
     *     label?: string[]|Stringable[]|null,
     *     load?: boolean|null,
     *     metadataFile?: string|Stringable|null,
     *     network?: string|Stringable|null,
     *     noCache?: boolean|null,
     *     noCacheFilter?: string|Stringable|null,
     *     output?: string|Stringable|null,
     *     platform?: string|Stringable|null,
     *     progress?: string|Stringable|null,
     *     provenance?: string|Stringable|null,
     *     pull?: boolean|null,
     *     push?: boolean|null,
     *     quiet?: boolean|null,
     *     sbom?: string|Stringable|null,
     *     secret?: string[]|Stringable[]|null,
     *     shmSize?: string|Stringable|null,
     *     ssh?: string|Stringable|null,
     *     tag?: string|Stringable|null,
     *     target?: string|Stringable|null,
     *     ulimit?: string|Stringable|null,
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
