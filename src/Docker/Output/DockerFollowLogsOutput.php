<?php

namespace Testcontainers\Docker\Output;

use Generator;
use Symfony\Component\Process\Process;
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
     * @return Generator<string, null|string> an iterator to traverse the lines of the Docker logs output
     */
    public function getIterator()
    {
        self::ensureInstanceOf($this->process, Process::class);

        $lines = new LineIterator($this->process);

        return $lines->getIterator();
    }
}
