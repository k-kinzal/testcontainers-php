<?php

namespace Testcontainers\Containers\GenericContainer;

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
use Testcontainers\Utility\WithLogger;

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
    use ReuseModeSetting;
    use SSHPortForwardSetting;
    use StartupSetting;
    use VolumesFromSetting;
    use WaitSetting;
    use WorkdirSetting;
    use WithLogger;

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
        if ($image === null && static::$IMAGE === null) {
            throw new \InvalidArgumentException('Unexpectedly image and static::$IMAGE are both null');
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

        $this->logger()->debug('Starting container');

        $maxRetryAttempts = $this->startupConflictRetryAttempts();
        $retryCount = 0;
        while ($retryCount < $maxRetryAttempts) {
            try {
                if ($timeout !== null) {
                    $output = $client->withLogger($this->logger())->withTimeout($timeout)->run($image, $command, $args, $options);
                } else {
                    $output = $client->withLogger($this->logger())->run($image, $command, $args, $options);
                }
                if (!$output instanceof DockerRunWithDetachOutput) {
                    throw new \LogicException('Expected DockerRunWithDetachOutput');
                }

                break; // Success, exit the retry loop
            } catch (PortAlreadyAllocatedException $e) {
                if ($portStrategy === null) {
                    throw $e;
                }
                $behavior = $portStrategy->conflictBehavior();
                if ($behavior->isRetry()) {
                    if ($retryCount >= $maxRetryAttempts) {
                        $this->logger()->error('Maximum retry attempts reached for port allocation', [
                            'retryCount' => $retryCount,
                            'maxRetries' => $maxRetryAttempts,
                            'exception' => $e,
                        ]);

                        throw $e;
                    }
                    $this->logger()->debug('Port already allocated, retrying: '.$e->getMessage(), [
                        'exception' => $e,
                        'retryCount' => $retryCount + 1,
                        'maxRetries' => $maxRetryAttempts,
                    ]);
                    ++$retryCount;

                    continue; // Retry the loop
                }
                if ($behavior->isFail()) {
                    throw $e;
                }

                throw new \LogicException('Unknown conflict behavior: `'.$behavior.'`', 0, $e);
            } catch (BindAddressAlreadyUseException $e) {
                if ($portStrategy === null) {
                    throw $e;
                }
                $behavior = $portStrategy->conflictBehavior();
                if ($behavior->isRetry()) {
                    if ($retryCount >= $maxRetryAttempts) {
                        $this->logger()->error('Maximum retry attempts reached for bind address', [
                            'retryCount' => $retryCount,
                            'maxRetries' => $maxRetryAttempts,
                            'exception' => $e,
                        ]);

                        throw $e;
                    }
                    $this->logger()->debug('Bind address already in use, retrying: '.$e->getMessage(), [
                        'exception' => $e,
                        'retryCount' => $retryCount + 1,
                        'maxRetries' => $maxRetryAttempts,
                    ]);
                    ++$retryCount;

                    continue; // Retry the loop
                }
                if ($behavior->isFail()) {
                    throw $e;
                }

                throw new \LogicException('Unknown conflict behavior: `'.$behavior.'`', 0, $e);
            }
        }

        $this->logger()->debug('Container started', [
            'containerId' => $output->getContainerId(),
        ]);

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
            $this->logger()->debug('Waiting for container to start', [
                'strategy' => $startupCheckStrategy,
            ]);
            if ($startupCheckStrategy->withLogger($this->logger())->waitUntilStartupSuccessful($instance) === false) {
                throw new \RuntimeException('failed startup check: illegal state of container');
            }
            $this->logger()->debug('Container started successfully');
        }

        if (count($ports) > 0) {
            $sshPortForward = $this->sshPortForward();
            if ($sshPortForward) {
                $port = $instance->getMappedPort(array_keys($ports)[0]);
                if ($port) {
                    $remoteHost = Environments::TESTCONTAINERS_SSH_FEEDFORWARDING_REMOTE_HOST_OVERRIDE();
                    if ($remoteHost === null) {
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
                    $this->logger()->debug('Opening SSH tunnel');
                    $session = $tunnel->open();
                    $instance->setData($session);
                    $this->logger()->debug('SSH tunnel opened', [
                        'session' => $session,
                    ]);
                }
            }
        }

        $waitStrategy = $this->waitStrategy($instance);
        if ($waitStrategy) {
            $this->logger()->debug('Waiting for container to be ready', [
                'strategy' => $waitStrategy,
            ]);
            $waitStrategy->withLogger($this->logger())->waitUntilReady($instance);
        }

        $this->logger()->debug('Container is ready');

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
