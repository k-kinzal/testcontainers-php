<?php

namespace Testcontainers\Docker;

use Testcontainers\Docker\Command\BaseCommand;
use Testcontainers\Docker\Command\BuildCommand;
use Testcontainers\Docker\Command\InspectCommand;
use Testcontainers\Docker\Command\LogsCommand;
use Testcontainers\Docker\Command\NetworkCreateCommand;
use Testcontainers\Docker\Command\RunCommand;
use Testcontainers\Docker\Command\StopCommand;

/**
 * A client for interacting with Docker.
 *
 * This client provides a simple interface for executing Docker commands from PHP.
 * It allows you to run containers, stop containers, and retrieve the status of running containers.
 */
class DockerClient
{
    use BaseCommand;
    use BuildCommand;
    use InspectCommand;
    use LogsCommand;
    use NetworkCreateCommand;
    use RunCommand;
    use StopCommand;

    public function __clone()
    {
        return (new self())
            ->withCommand($this->command)
            ->withGlobalOptions($this->options)
            ->withCwd($this->cwd)
            ->withEnv($this->env)
            ->withInput($this->input)
            ->withTimeout($this->timeout)
            ->withProcOptions($this->proc_options);
    }
}
