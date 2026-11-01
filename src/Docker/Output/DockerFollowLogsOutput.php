<?php

namespace Testcontainers\Docker\Output;

use Testcontainers\Utility\LineIterator;

/**
 * Represents the output of a Docker `logs` command executed via Symfony Process.
 *
 * This class extends DockerOutput to provide specific handling for container logs.
 * @api
 */
class DockerFollowLogsOutput extends DockerOutput
{
    /**
     * Returns an iterator for the Docker logs output line by line.
     *
     * @return \Generator<string, null|string> an iterator to traverse the lines of the Docker logs output
     */
    public function getIterator()
    {
        $lines = new LineIterator(self::ensureInstanceOf($this->process, \Symfony\Component\Process\Process::class));

        return $lines->getIterator();
    }
}
