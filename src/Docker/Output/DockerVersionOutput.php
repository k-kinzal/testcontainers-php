<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;

/**
 * Represents the output of a Docker `version` command executed via Symfony Process.
 *
 * This class extends DockerOutput to parse the JSON output from `docker version --format json`
 * and provide access to version information.
 */
final class DockerVersionOutput extends DockerOutput
{
    /**
     * The parsed version data.
     *
     * @var array{Client?: array{Version: string}, Server?: array{Version: string}}
     */
    private $data;

    /**
     * @param Process $process the Symfony Process instance that executed the `docker version` command
     *
     * @throws InvalidValueException if a version value is not a string
     */
    public function __construct($process)
    {
        parent::__construct($process);

        $output = json_decode($process->getOutput(), true);
        $this->data = [];
        if (!is_array($output)) {
            return;
        }

        foreach (['Client', 'Server'] as $component) {
            if (!isset($output[$component]['Version'])) {
                continue;
            }
            if (!is_string($output[$component]['Version'])) {
                throw new InvalidValueException(
                    sprintf('Docker %s version is not a string', $component),
                    ['output' => $output]
                );
            }

            $this->data[$component] = ['Version' => $output[$component]['Version']];
        }
    }

    /**
     * Get the Docker Client version string.
     *
     * @return null|string the client version (e.g. "28.0.1"), or null if unavailable
     */
    public function getClientVersion()
    {
        return isset($this->data['Client']['Version']) ? $this->data['Client']['Version'] : null;
    }

    /**
     * Get the Docker Server version string.
     *
     * @return null|string the server version (e.g. "28.0.1"), or null if unavailable
     */
    public function getServerVersion()
    {
        return isset($this->data['Server']['Version']) ? $this->data['Server']['Version'] : null;
    }
}
