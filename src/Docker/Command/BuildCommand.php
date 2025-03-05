<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Output\DockerBuildOutput;

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
     * @param string $path The path to the directory containing the Dockerfile.
     * @param array{
     *     addHost?: string[],
     *     allow?: string[],
     *     annotation?: string[],
     *     attest?: string[],
     *     buildArg?: string[],
     *     buildContext?: string,
     *     builder?: string,
     *     cacheFrom?: string[],
     *     cacheTo?: string[],
     *     call?: string,
     *     cgroupParent?: string,
     *     check?: boolean,
     *     debug?: boolean,
     *     file?: string,
     *     iidfile?: string,
     *     label?: string[],
     *     load?: boolean,
     *     metadataFile?: string,
     *     network?: string,
     *     noCache?: boolean,
     *     noCacheFilter?: string,
     *     output?: string,
     *     platform?: string,
     *     progress?: string,
     *     provenance?: string,
     *     pull?: boolean,
     *     push?: boolean,
     *     quiet?: boolean,
     *     sbom?: string,
     *     secret?: string[],
     *     shmSize?: string,
     *     ssh?: string,
     *     tag?: string,
     *     target?: string,
     *     ulimit?: string,
     * } $options Additional options for the Docker command.
     * @return DockerBuildOutput The output of the Docker build command.
     *
     * @throws DockerException If the Docker command fails for any other reason.
     */
    public function build($path, $options = [])
    {
        $process = $this->execute('build', null, [$path], $options);
        return new DockerBuildOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
