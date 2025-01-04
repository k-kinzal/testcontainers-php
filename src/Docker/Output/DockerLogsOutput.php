<?php

namespace Testcontainers\Docker\Output;

use Testcontainers\Docker\Output\DockerOutput;

/**
 * Represents the output of a Docker `logs` command executed via Symfony Process.
 *
 * This class extends DockerOutput to provide specific handling for container logs.
 */
class DockerLogsOutput extends DockerOutput
{
}
