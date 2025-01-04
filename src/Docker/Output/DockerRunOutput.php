<?php

namespace Testcontainers\Docker\Output;

use Testcontainers\Docker\Output\DockerOutput;

/**
 * Represents the output of a Docker `run` command executed via Symfony Process.
 *
 * This class extends DockerOutput to provide methods for retrieving the standard output,
 * error output, and exit code of the Docker `run` command.
 */
class DockerRunOutput extends DockerOutput
{
}
