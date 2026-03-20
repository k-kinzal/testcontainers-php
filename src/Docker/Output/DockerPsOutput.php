<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Types\ContainerId;

/**
 * Represents the output of a Docker `ps` command executed via Symfony Process.
 *
 * This class extends DockerOutput to parse container listings with their labels.
 */
class DockerPsOutput extends DockerOutput
{
    /**
     * Parsed container entries from the ps output.
     *
     * @var array<int, array{id: ContainerId, labels: array<string, string>}>
     */
    private $containers;

    /**
     * @param Process $process
     */
    public function __construct($process)
    {
        parent::__construct($process);

        $output = $process->getOutput();
        $this->containers = [];

        foreach (explode("\n", $output) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $parts = explode("\t", $line, 2);
            $id = trim($parts[0]);
            $labelsStr = isset($parts[1]) ? trim($parts[1]) : '';

            $labels = [];
            if (!empty($labelsStr)) {
                foreach (explode(',', $labelsStr) as $labelPair) {
                    $kv = explode('=', $labelPair, 2);
                    if (count($kv) === 2) {
                        $labels[trim($kv[0])] = trim($kv[1]);
                    }
                }
            }

            $this->containers[] = [
                'id' => new ContainerId($id),
                'labels' => $labels,
            ];
        }
    }

    /**
     * Get all container entries.
     *
     * @return array<int, array{id: ContainerId, labels: array<string, string>}>
     */
    public function getContainers()
    {
        return $this->containers;
    }
}
