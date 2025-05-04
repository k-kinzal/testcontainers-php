<?php

namespace Testcontainers\Docker\Output;

/**
 * Represents the output of a Docker `logs` command executed via Symfony Process.
 *
 * This class extends DockerOutput to provide specific handling for container logs.
 */
class DockerFollowLogsOutput extends DockerOutput
{
    /**
     * Returns an iterator for the Docker logs output.
     *
     * @return \Generator<string> an iterator to traverse the Docker logs output
     */
    public function getIterator()
    {
        return $this->process->getIterator();
    }
}
