<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Types\ContainerListItem;

use function Testcontainers\ensure;

/**
 * Represents the output of a Docker `ps` command executed via Symfony Process.
 *
 * This class extends DockerOutput to parse the JSON output of `docker ps --format json`
 * into typed ContainerListItem objects.
 */
class DockerPsOutput extends DockerOutput
{
    /**
     * Parsed container entries from the ps output.
     *
     * @var ContainerListItem[]
     */
    private $containers;

    /**
     * @param Process $process the Symfony Process instance that executed the `docker ps` command
     *
     * @throws InvalidValueException if the process output cannot be parsed as JSON lines
     */
    public function __construct($process)
    {
        ensure($process instanceof Process, '$process must be Process');

        parent::__construct($process);

        $this->containers = $this->deserialize($process->getOutput());
    }

    /**
     * Get all container entries.
     *
     * @return ContainerListItem[]
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Deserialize the JSON-lines output of `docker ps --format json`.
     *
     * Docker outputs one JSON object per line (JSON Lines format), not a JSON array.
     *
     * @param string $output the raw output from the docker ps command
     *
     * @return ContainerListItem[]
     *
     * @throws InvalidValueException if a line is not valid JSON or ContainerListItem rejects the entry
     */
    private function deserialize($output)
    {
        $containers = [];

        foreach (explode("\n", $output) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $decoded = json_decode($line, true);
            if (!is_array($decoded)) {
                throw new InvalidValueException(
                    'Docker ps output line is not valid JSON',
                    ['output' => $line]
                );
            }

            $containers[] = ContainerListItem::fromArray($decoded);
        }

        return $containers;
    }
}
