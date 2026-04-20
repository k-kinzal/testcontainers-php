<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;

use function Testcontainers\ensure;

/**
 * Represents the output of a Docker `version` command executed via Symfony Process.
 *
 * This class extends DockerOutput to parse the JSON output from `docker version --format json`
 * and provide access to version information.
 */
class DockerVersionOutput extends DockerOutput
{
    /**
     * The parsed version data.
     *
     * @var array{Client?: array{Version?: string}, Server?: array{Version?: string}}
     */
    private $data;

    /**
     * @param Process $process the Symfony Process instance that executed the `docker version` command
     */
    public function __construct($process)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure($process instanceof Process, '$process must be Process');

        parent::__construct($process);

        /** @var null|array{Client?: array{Version?: string}, Server?: array{Version?: string}} $output */
        $output = json_decode($process->getOutput(), true);
        $this->data = is_array($output) ? $output : [];
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
