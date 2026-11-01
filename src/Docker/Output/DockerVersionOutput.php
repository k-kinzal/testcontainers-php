<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;

/**
 * Represents the output of a Docker `version` command executed via Symfony Process.
 *
 * This class extends DockerOutput to parse the JSON output from `docker version --format json`
 * and provide access to version information.
 * @api
 */
class DockerVersionOutput extends DockerOutput
{
    /**
     * The parsed version data.
     *
     * @var array
     */
    private $data;

    /**
     * @param Process $process the Symfony Process instance that executed the `docker version` command
     */
    public function __construct($process)
    {
        parent::__construct($process);

        $output = self::ensureNullableArray(json_decode($process->getOutput(), true));

        $this->data = $output === null ? [] : $output;
    }

    /**
     * Get the Docker Client version string.
     *
     * @return null|string the client version (e.g. "28.0.1"), or null if unavailable
     */
    public function getClientVersion()
    {
        return isset($this->data['Client']['Version']) ? self::ensureString($this->data['Client']['Version']) : null;
    }

    /**
     * Get the Docker Server version string.
     *
     * @return null|string the server version (e.g. "28.0.1"), or null if unavailable
     */
    public function getServerVersion()
    {
        return isset($this->data['Server']['Version']) ? self::ensureString($this->data['Server']['Version']) : null;
    }
}
