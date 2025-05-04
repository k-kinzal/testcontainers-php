<?php

namespace Testcontainers\Containers\GenericContainer;

use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Testcontainers\Containers\Container;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\BindAddressAlreadyUseException;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Environments;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\SSH\Tunnel;

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
    use SSHPortForwardSetting;
    use StartupSetting;
    use VolumesFromSetting;
    use WaitSetting;
    use WorkdirSetting;

    /**
     * The Docker client.
     *
     * @var null|DockerClient
     */
    private $client;

    /**
     * @param null|string $image the image to be used for the container
     */
    public function __construct($image = null)
    {
        if (null === $image && null === static::$IMAGE) {
            throw new InvalidArgumentException('Unexpectedly image and static::$IMAGE are both null');
        }

        $this->image = $image ?: static::$IMAGE;
    }

    /**
     * Set the Docker client.
     *
     * @param DockerClient $client the Docker client
     *
     * @return self
     */
    public function withDockerClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidFormatException if the provided mode is not valid
     * @throws DockerException        if the Docker command fails
     */
    public function start()
    {
        $client = $this->client();

        $portStrategy = $this->portStrategy();
        $ports = $this->ports();

        $image = $this->image();
        $command = $this->command();
        $args = $this->args();
        $options = [
            'addHost' => $this->extraHosts(),
            'detach' => true,
            'env' => $this->env(),
            'label' => $this->labels(),
            'mount' => $this->mounts(),
            'name' => $this->name(),
            'network' => $this->networkMode(),
            'networkAlias' => $this->networkAliases(),
            'publish' => array_map(function ($containerPort, $hostPort) {
                return $hostPort.':'.$containerPort;
            }, array_keys($ports), array_values($ports)),
            'pull' => $this->pullPolicy(),
            'privileged' => $this->privileged(),
            'volumesFrom' => $this->volumesFrom(),
            'workdir' => $this->workDir(),
        ];
        $timeout = $this->startupTimeout();

        try {
            if (null !== $timeout) {
                $output = $client->withTimeout($timeout)->run($image, $command, $args, $options);
            } else {
                $output = $client->run($image, $command, $args, $options);
            }
            if (!($output instanceof DockerRunWithDetachOutput)) {
                throw new LogicException('Expected DockerRunWithDetachOutput');
            }
        } catch (PortAlreadyAllocatedException $e) {
            if (null === $portStrategy) {
                throw $e;
            }
            $behavior = $portStrategy->conflictBehavior();
            if ($behavior->isRetry()) {
                return $this->start();
            }
            if ($behavior->isFail()) {
                throw $e;
            }

            throw new LogicException('Unknown conflict behavior: `'.$behavior.'`', 0, $e);
        } catch (BindAddressAlreadyUseException $e) {
            if (null === $portStrategy) {
                throw $e;
            }
            $behavior = $portStrategy->conflictBehavior();
            if ($behavior->isRetry()) {
                return $this->start();
            }
            if ($behavior->isFail()) {
                throw $e;
            }

            throw new LogicException('Unknown conflict behavior: `'.$behavior.'`', 0, $e);
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

        $startupCheckStrategy = $this->startupCheckStrategy($instance);
        if ($startupCheckStrategy) {
            if (false === $startupCheckStrategy->waitUntilStartupSuccessful($instance)) {
                throw new RuntimeException('failed startup check: illegal state of container');
            }
        }

        if (count($ports) > 0) {
            $sshPortForward = $this->sshPortForward();
            if ($sshPortForward) {
                $port = $instance->getMappedPort(array_keys($ports)[0]);
                if ($port) {
                    $remoteHost = Environments::TESTCONTAINERS_SSH_FEEDFORWARDING_REMOTE_HOST_OVERRIDE();
                    if (null === $remoteHost) {
                        $remoteHost = '127.0.0.1';
                    }
                    $sshHost = isset($sshPortForward['sshHost']) ? $sshPortForward['sshHost'] : $instance->getHost();
                    $sshUser = isset($sshPortForward['sshUser']) ? $sshPortForward['sshUser'] : null;
                    $sshPort = isset($sshPortForward['sshPort']) ? $sshPortForward['sshPort'] : null;
                    $tunnel = (new Tunnel($port, $remoteHost, $port, $sshHost));
                    if ($sshUser) {
                        $tunnel->withUser($sshUser);
                    }
                    if ($sshPort) {
                        $tunnel->withSshPort($sshPort);
                    }
                    $session = $tunnel->open();
                    $instance->setData($session);
                }
            }
        }

        $waitStrategy = $this->waitStrategy($instance);
        if ($waitStrategy) {
            $waitStrategy->waitUntilReady($instance);
        }

        return $instance;
    }

    /**
     * Get the Docker client.
     *
     * @return DockerClient
     */
    protected function client()
    {
        return $this->client ?: DockerClientFactory::create();
    }
}
