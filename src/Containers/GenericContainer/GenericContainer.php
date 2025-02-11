<?php

namespace Testcontainers\Containers\GenericContainer;

use LogicException;
use RuntimeException;
use Testcontainers\Containers\Container;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\BindAddressAlreadyUseException;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * GenericContainer is a generic implementation of docker container.
 */
class GenericContainer implements Container
{
    use EnvSetting;
    use GeneralSetting;
    use HostSetting;
    use LabelSetting;
    use MountSetting;
    use NetworkAliasSetting;
    use NetworkModeSetting;
    use PortSetting;
    use PrivilegeSetting;
    use PullPolicySetting;
    use StartupSetting;
    use VolumesFromSetting;
    use WaitSetting;
    use WorkdirSetting;

    /**
     * The Docker client.
     * @var DockerClient|null
     */
    private $client;

    /**
     * The commands to be executed in the container.
     * @var null|string|string[]
     */
    protected static $COMMANDS;

    /**
     * The commands to be executed in the container.
     * @var string[]
     */
    private $commands = [];

    /**
     * @param string|null $image The image to be used for the container.
     */
    public function __construct($image = null)
    {
        assert($image || static::$IMAGE);

        $this->image = $image ?: static::$IMAGE;
    }

    /**
     * Set the Docker client.
     * @param DockerClient $client The Docker client.
     * @return self
     */
    public function withDockerClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withCommand($cmd)
    {
        $this->commands = [$cmd];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withCommands($commandParts)
    {
        $this->commands = $commandParts;

        return $this;
    }

    /**
     * Retrieve the command to be executed in the container.
     *
     * This method returns the command that should be executed in the container.
     * If a specific command is set, it will return that. Otherwise, it will
     * attempt to retrieve the default command from the provider.
     *
     * @return string|string[]|null
     */
    protected function commands()
    {
        if (static::$COMMANDS) {
            return static::$COMMANDS;
        }
        if ($this->commands) {
            return $this->commands;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidFormatException If the provided mode is not valid.
     */
    public function start()
    {
        $commands = $this->commands();
        if (is_string($commands)) {
            $commands = [$commands];
        }
        $command = null;
        $args = [];
        if (is_array($commands)) {
            $command = $commands[0];
            if (count($commands) > 1) {
                $args = array_slice($commands, 1);
            }
        }

        $extraHosts = $this->extraHosts();
        $hosts = [];
        if ($extraHosts) {
            foreach ($extraHosts as $host) {
                $hosts[] = $host->toString();
            }
        }

        $portStrategy = $this->portStrategy();
        $ports = $this->ports();
        $client = $this->client ?: DockerClientFactory::create();

        try {
            $options = [
                'addHost' => $hosts,
                'detach' => true,
                'env' => $this->env(),
                'label' => $this->labels(),
                'mount' => $this->mounts(),
                'network' => $this->networkMode(),
                'networkAlias' => $this->networkAliases(),
                'volumesFrom' => $this->volumesFrom(),
                'publish' => array_map(function ($containerPort, $hostPort) {
                    return $hostPort . ':' . $containerPort;
                }, array_keys($ports), array_values($ports)),
                'pull' => $this->pullPolicy(),
                'workdir' => $this->workDir(),
                'privileged' => $this->privileged(),
                'name' => $this->name(),
            ];
            $timeout = $this->startupTimeout();
            if ($timeout !== null) {
                $output = $client->withTimeout($timeout)->run($this->image(), $command, $args, $options);
            } else {
                $output = $client->run($this->image(), $command, $args, $options);
            }
        } catch (PortAlreadyAllocatedException $e) {
            if ($portStrategy === null) {
                throw $e;
            }
            $behavior = $portStrategy->conflictBehavior();
            if ($behavior->isRetry()) {
                return $this->start();
            }
            if ($behavior->isFail()) {
                throw $e;
            }
            throw new LogicException('Unknown conflict behavior: `' . $behavior . '`', 0, $e);
        } catch (BindAddressAlreadyUseException $e) {
            if ($portStrategy === null) {
                throw $e;
            }
            $behavior = $portStrategy->conflictBehavior();
            if ($behavior->isRetry()) {
                return $this->start();
            }
            if ($behavior->isFail()) {
                throw $e;
            }
            throw new LogicException('Unknown conflict behavior: `' . $behavior . '`', 0, $e);
        }
        if (!($output instanceof DockerRunWithDetachOutput)) {
            throw new LogicException('Expected DockerRunWithDetachOutput');
        }
        if ($output->getExitCode() !== 0) {
            throw new RuntimeException('Failed to start container');
        }
        $containerDef = [
            'containerId' => $output->getContainerId(),
            'labels' => $this->labels(),
            'ports' => $ports,
            'pull' => $this->pullPolicy(),
            'privileged' => $this->privileged(),
        ];
        $instance = new GenericContainerInstance($containerDef);
        $instance->setDockerClient($client);

        $startupCheckStrategy = $this->startupCheckStrategy();
        if ($startupCheckStrategy) {
            if ($startupCheckStrategy->waitUntilStartupSuccessful($instance) === false) {
                throw new RuntimeException('Illegal state of container');
            }
        }

        $waitStrategy = $this->waitStrategy($instance);
        if ($waitStrategy) {
            $waitStrategy->waitUntilReady($instance);
        }

        return $instance;
    }
}
